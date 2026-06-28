#!/usr/bin/env node
//
// Idempotent Zitadel provisioner.
//
// Creates the default project and its clients, then prints + writes the
// resulting IDs/secrets:
//   - one OIDC app per frontend  (User-Agent / PKCE, no secret)
//   - one API  app per backend   (Basic auth — used for token introspection)
//
// Safe to re-run: every resource is searched before it is created, so nothing
// is ever duplicated. Talks to Zitadel over the internal compose network using
// the PAT the core writes to a shared volume on first init
// (ZITADEL_FIRSTINSTANCE_PATPATH). Uses node:http(s) directly (not fetch) so the
// Host header — which Zitadel uses to resolve the instance — is set reliably.
//
// No npm dependencies: runs on a stock `node:22-alpine` image.

import { access, readFile, writeFile } from 'node:fs/promises'
import http from 'node:http'
import https from 'node:https'

// ZITADEL_API_URL: where to reach the instance (internal http://zitadel:8080 on
// the Dokploy host, or a public https URL when run from CI/local).
const API_URL = process.env.ZITADEL_API_URL ?? process.env.ZITADEL_API_INTERNAL_URL ?? 'http://zitadel:8080'
// Host header — Zitadel resolves the instance from it. Defaults to the URL host.
const API_HOST = process.env.ZITADEL_API_HOST ?? new URL(API_URL).hostname
// PAT: prefer an injected ZITADEL_PAT (CI/local), else the file the core writes.
const PAT_ENV = process.env.ZITADEL_PAT?.trim()
const PAT_FILE = process.env.ZITADEL_PAT_FILE ?? '/pat/provisioner.pat'
const OUT_FILE = process.env.ZITADEL_CLIENTS_OUT ?? ''
const CONFIG_FILE = process.env.ZITADEL_APPS_CONFIG ?? new URL('./apps.json', import.meta.url)
const DEV_MODE = String(process.env.ZITADEL_EXTERNALSECURE) === 'false'

const url = new URL(API_URL)
const transport = url.protocol === 'https:' ? https : http
const sleep = (ms) => new Promise((r) => setTimeout(r, ms))
const envKey = (s) => s.toUpperCase().replace(/[^A-Z0-9]+/g, '_')

let PAT // populated by readPat()

function request(method, path, body) {
  const payload = body ? JSON.stringify(body) : null
  const options = {
    protocol: url.protocol,
    hostname: url.hostname,
    port: url.port || (url.protocol === 'https:' ? 443 : 80),
    path,
    method,
    headers: {
      Accept: 'application/json',
      Host: API_HOST, // instance resolution — must match ExternalDomain
      ...(PAT ? { Authorization: `Bearer ${PAT}` } : {}),
      ...(payload
        ? { 'Content-Type': 'application/json', 'Content-Length': Buffer.byteLength(payload) }
        : {}),
    },
  }
  return new Promise((resolve, reject) => {
    const req = transport.request(options, (res) => {
      let data = ''
      res.on('data', (c) => (data += c))
      res.on('end', () => resolve({ status: res.statusCode, body: parse(data) }))
    })
    req.on('error', reject)
    if (payload) req.write(payload)
    req.end()
  })
}

function parse(s) {
  if (!s) return null
  try {
    return JSON.parse(s)
  } catch {
    return s
  }
}

async function waitReady(timeoutMs = 180_000) {
  const start = Date.now()
  while (Date.now() - start < timeoutMs) {
    try {
      const r = await request('GET', '/debug/ready')
      if (r.status === 200) return
    } catch {
      /* not up yet */
    }
    process.stdout.write('.')
    await sleep(3000)
  }
  throw new Error('Zitadel did not become ready in time')
}

async function readPat(timeoutMs = 180_000) {
  if (PAT_ENV) return PAT_ENV
  const start = Date.now()
  while (Date.now() - start < timeoutMs) {
    try {
      await access(PAT_FILE)
      const pat = (await readFile(PAT_FILE, 'utf8')).trim()
      if (pat) return pat
    } catch {
      /* PAT not written yet */
    }
    await sleep(3000)
  }
  throw new Error(`PAT file ${PAT_FILE} not found in time`)
}

async function ensureProject(name) {
  const search = await request('POST', '/management/v1/projects/_search', {
    queries: [{ nameQuery: { name, method: 'TEXT_QUERY_METHOD_EQUALS' } }],
  })
  const found = search.body?.result?.find((p) => p.name === name)
  if (found) {
    console.log(`= project "${name}" (${found.id})`)
    return found.id
  }
  const created = await request('POST', '/management/v1/projects', { name })
  if (!created.body?.id) throw new Error(`create project failed: ${JSON.stringify(created)}`)
  console.log(`+ project "${name}" (${created.body.id})`)
  return created.body.id
}

async function findApp(projectId, name) {
  const search = await request('POST', `/management/v1/projects/${projectId}/apps/_search`, {
    queries: [{ nameQuery: { name, method: 'TEXT_QUERY_METHOD_EQUALS' } }],
  })
  return search.body?.result?.find((a) => a.name === name)
}

function frontendRedirects(base, authName) {
  const root = base.endsWith('/') ? base : `${base}/`
  return {
    redirectUris: [
      `${root}auth/signinwin/${authName}`,
      `${root}auth/signinsilent/${authName}`,
      `${root}auth/signinpop/${authName}`,
    ],
    postLogoutRedirectUris: [root, `${root}auth/signoutpop/${authName}`],
  }
}

async function ensureFrontend(projectId, app, out) {
  const base = process.env[app.baseUrlEnv]
  if (!base) {
    console.warn(`! skip frontend "${app.name}" — ${app.baseUrlEnv} is not set`)
    return
  }
  const existing = await findApp(projectId, app.name)
  if (existing) {
    const clientId = existing.oidcConfig?.clientId
    console.log(`= frontend "${app.name}" (clientId=${clientId ?? 'unknown'})`)
    if (clientId) out[`${envKey(app.name)}__VITE_ZITADEL_CLIENT_ID`] = clientId
    return
  }
  const { redirectUris, postLogoutRedirectUris } = frontendRedirects(base, app.authName ?? 'zitadel')
  const res = await request('POST', `/management/v1/projects/${projectId}/apps/oidc`, {
    name: app.name,
    redirectUris,
    postLogoutRedirectUris,
    responseTypes: ['OIDC_RESPONSE_TYPE_CODE'],
    grantTypes: ['OIDC_GRANT_TYPE_AUTHORIZATION_CODE', 'OIDC_GRANT_TYPE_REFRESH_TOKEN'],
    appType: 'OIDC_APP_TYPE_USER_AGENT',
    authMethodType: 'OIDC_AUTH_METHOD_TYPE_NONE',
    accessTokenType: 'OIDC_TOKEN_TYPE_BEARER',
    devMode: DEV_MODE,
  })
  if (!res.body?.clientId) throw new Error(`create frontend "${app.name}" failed: ${JSON.stringify(res)}`)
  console.log(`+ frontend "${app.name}" (clientId=${res.body.clientId})`)
  out[`${envKey(app.name)}__VITE_ZITADEL_CLIENT_ID`] = res.body.clientId
}

async function ensureBackend(projectId, app, out) {
  const existing = await findApp(projectId, app.name)
  if (existing) {
    const clientId = existing.apiConfig?.clientId
    console.log(`= backend "${app.name}" (clientId=${clientId ?? 'unknown'}) — secret not re-readable, regenerate in console if lost`)
    if (clientId) out[`${envKey(app.name)}__ZITADEL_CLIENT_ID`] = clientId
    return
  }
  const res = await request('POST', `/management/v1/projects/${projectId}/apps/api`, {
    name: app.name,
    authMethodType: 'API_AUTH_METHOD_TYPE_BASIC',
  })
  if (!res.body?.clientId) throw new Error(`create backend "${app.name}" failed: ${JSON.stringify(res)}`)
  console.log(`+ backend "${app.name}" (clientId=${res.body.clientId})`)
  out[`${envKey(app.name)}__ZITADEL_CLIENT_ID`] = res.body.clientId
  if (res.body.clientSecret) out[`${envKey(app.name)}__ZITADEL_CLIENT_SECRET`] = res.body.clientSecret
}

async function main() {
  console.log(`> provisioning via ${API_URL} (Host: ${API_HOST})`)
  process.stdout.write('> waiting for readiness ')
  await waitReady()
  console.log(' ok')
  PAT = await readPat()
  console.log('> PAT loaded')

  const config = JSON.parse(await readFile(CONFIG_FILE, 'utf8'))
  const projectId = await ensureProject(config.projectName)

  // The project id is both the frontend "project resource id" and the backend
  // "project id" — same value feeds VITE_ZITADEL_PROJECT_RESOURCE_ID and
  // ZITADEL_PROJECT_ID, which is what keeps token audiences aligned.
  const out = { ZITADEL_PROJECT_ID: projectId, VITE_ZITADEL_PROJECT_RESOURCE_ID: projectId }

  for (const app of config.frontends ?? []) await ensureFrontend(projectId, app, out)
  for (const app of config.backends ?? []) await ensureBackend(projectId, app, out)

  const rendered = `${Object.entries(out)
    .map(([k, v]) => `${k}=${v}`)
    .join('\n')}\n`
  if (OUT_FILE) {
    try {
      await writeFile(OUT_FILE, rendered)
      console.log(`> wrote ${OUT_FILE}`)
    } catch (e) {
      console.warn(`! could not write ${OUT_FILE}: ${e.message}`)
    }
  }

  console.log('\n=== CLIENTS (copy into each app/api environment) ===')
  console.log(rendered)
  console.log('Frontends need: VITE_ZITADEL_CLIENT_ID + VITE_ZITADEL_PROJECT_RESOURCE_ID + VITE_ZITADEL_ISSUER')
  console.log('Backends  need: ZITADEL_CLIENT_ID + ZITADEL_CLIENT_SECRET + ZITADEL_PROJECT_ID + ZITADEL_DOMAIN')
}

main().catch((e) => {
  console.error('\nx provisioning failed:', e.message)
  process.exit(1)
})
