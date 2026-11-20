# Shared Caddy configurations

One Caddyfile per kind of project, which every project of that kind is served with by default.
A project adds to it, or replaces it, from its own directory: it never copies it.

| Config                                          | Projects                                                     | Copied by                                                          |
| ----------------------------------------------- | ------------------------------------------------------------ | ------------------------------------------------------------------ |
| `pwa/Caddyfile`                                 | Frontends tagged `pwa` (an app shell and its service worker) | `libs/docker/frontend/Dockerfile`                                  |
| `ssg/Caddyfile`                                 | Frontends tagged `ssg` (vite-ssg websites), storybook        | `libs/docker/frontend/Dockerfile`, `projects/storybook/Dockerfile` |
| `symfony/Caddyfile`, `symfony/worker.Caddyfile` | Symfony APIs (FrankenPHP)                                    | `libs/docker/symfony/Dockerfile`                                   |

## Default, addition, replacement

A project's directory is the one its Caddyfile lives in: the project's root for a frontend,
`frankenphp/` for an API.

- **Default**: nothing in the project. Its image gets the shared Caddyfile.
- **Addition**: Caddyfile fragments (`<name>.Caddyfile`) in the project's `Caddyfile.d/`, one
  folder per place of the shared Caddyfile that imports them:

  | Folder                | Imported                                    | E.g.                              |
  | --------------------- | ------------------------------------------- | --------------------------------- |
  | `Caddyfile.d/global/` | in the global options block                 | `servers { trusted_proxies … }`   |
  | `Caddyfile.d/site/`   | in the project's site block                 | a `header`, a `redir`, a `handle` |
  | `Caddyfile.d/extra/`  | at the top level, before the project's site | another site block, a snippet     |

  flora's `frankenphp/Caddyfile.d/extra/flora-api.Caddyfile` is one. The APIs' Caddyfile also
  keeps FrankenPHP's environment placeholders for the same three places (`CADDY_GLOBAL_OPTIONS`,
  `CADDY_SERVER_EXTRA_DIRECTIVES`, `CADDY_EXTRA_CONFIG`), set at runtime.

- **Replacement**: the project's own `Caddyfile` (and, for an API, `worker.Caddyfile`), copied
  over the shared one. It may still import the project's `Caddyfile.d/` fragments, copied too.

A change to the shared Caddyfile reaches every project that keeps the default or adds to it. Add
to it before replacing it: a replaced Caddyfile no longer follows the shared one.

## How it gets into an image

The shared configs are the `caddy-config` build context of the image: the Dockerfile copies the
shared Caddyfile from there (`COPY --from=caddy-config`), then the project's `Caddyfile.d/` and
own `Caddyfile` from its build context, both optional.

- moon (`docker-build`, `docker-push`, `ci-test-image`): `.moon/scripts/docker-buildx.sh` passes
  `--build-context caddy-config=$SHARED_CADDY_CONFIG`, set per stack in `tag-pwa.yml`,
  `tag-ssg.yml` and `tag-symfony.yml`.
- compose (an API's dev container, flora's load test): `additional_contexts.caddy-config` in its
  `compose.override.yml` / `compose.load.yml`. The dev container also mounts the shared Caddyfile
  (and the API's `Caddyfile.d/` when it has one, or its own `Caddyfile` instead), so a change
  only needs a restart.

The Dockerfiles create an empty fragment in each `Caddyfile.d/` folder, which spares Caddy a
"No files matching import glob pattern" warning at every start. A dev container that mounts its
`Caddyfile.d/` hides them: the warning there is harmless.

Each config is a moon project (`caddy-pwa`, `caddy-ssg`, `caddy-symfony`) with no task, in the
`dependsOn` of every project whose image uses it, so that a change to it marks them affected:
their checks rerun and the deploy workflow redeploys them. A new project of one of these kinds
lists it too.

## Checking a Caddyfile

```bash
docker run --rm -v "$PWD/libs/caddy/ssg/Caddyfile:/etc/caddy/Caddyfile:ro" caddy:alpine caddy adapt --config /etc/caddy/Caddyfile
```
