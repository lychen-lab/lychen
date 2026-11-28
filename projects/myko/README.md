# Myko

Lychen's mushroom cultivation product. What it does: [`docs/myko/spec.fr.md`](../../docs/myko/spec.fr.md).
How it is built: [`CLAUDE.md`](CLAUDE.md).

## App (`projects/myko/app`, moon id `myko-app`)

```bash
moon myko-app:dev      # https://localhost:5820
moon myko-app:lint     # vue-tsc, then ESLint
moon myko-app:build
moon myko-app:docker-build
```

### Environment variables

The app reads them at runtime, as Espace does: `.env` maps each `VITE_*` variable to a
`${…}` placeholder, the build writes them to `dist/config.js`, and the image's entrypoint fills
them in from the container's environment. In development, Vite takes them from the shell or from
`projects/myko/app/.env.development.local` (git-ignored). A missing value breaks neither the
build nor the tests: without Zitadel, the sign-in screen (`/sign-in`) says that sign-in is not configured.

| Variable (container)          | In the app                         | What it is for                                                                                 |
| ----------------------------- | ---------------------------------- | ---------------------------------------------------------------------------------------------- |
| `APP_ID`                      | `VITE_APP_ID`                      | The application's id in the Lychen ecosystem.                                                  |
| `MYKO_API_HOST`               | `VITE_MYKO_API_HOST`               | Base URL of the Myko API (`projects/myko/api`), e.g. `https://localhost:8082`. Not called yet. |
| `MYKO_APP_URL`                | `VITE_MYKO_APP_URL`                | Optional. Origin of the QR codes' address. Unset: where the app runs. See below.               |
| `ZITADEL_ISSUER`              | `VITE_ZITADEL_ISSUER`              | URL of the Zitadel instance (OIDC issuer).                                                     |
| `ZITADEL_CLIENT_ID`           | `VITE_ZITADEL_CLIENT_ID`           | Client id of Myko's Zitadel application (PKCE, user agent).                                    |
| `ZITADEL_PROJECT_RESOURCE_ID` | `VITE_ZITADEL_PROJECT_RESOURCE_ID` | Resource id of the Zitadel project holding that application (audience and roles).              |
| —                             | `VITE_MYKO_FAKE_AUTH`              | Development only: `true` replaces Zitadel with a fake login. See below.                        |

### Origin of the QR codes

A label's QR code holds `<origin>/l/<log uuid>`, and that address never changes once printed. Set
`MYKO_APP_URL` to the address the labels must open for good (the production origin, say
`https://myko.lychen.org`): a label printed from a laptop or a staging build then still opens the real
app. Unset (or not an http(s) address), the origin is the one the app runs on, which is right for
local runs. The screens read it through `getMushroomLogQrOrigin()` (`src/config/mushroomLogQrOrigin.ts`).

### Signing in without Zitadel

`VITE_MYKO_FAKE_AUTH=true moon myko-app:dev` (or the line in `.env.development.local`) swaps
Zitadel for a fake login: "Se connecter" on the sign-in screen (every other screen sends an anonymous
visitor there) signs in a fake "Dev Myko" user, kept in the browser's
local storage, and sends no token to the API. The switch is read behind `import.meta.env.DEV`
(`src/auth/createAuth.ts`) and excluded from the runtime configuration (`vite.config.ts`), so a
production build contains neither the fake login nor the variable: after `moon myko-app:build`,
`grep -rniE 'fake|MYKO_FAKE_AUTH' projects/myko/app/dist` finds nothing.

### Zitadel application (to create)

A "User Agent" application with PKCE in the Lychen Zitadel project, whose redirect URIs follow
`libs/vue/zitadel` (`@zitadel/vue`, `vue-oidc-client`): redirect URIs
`<app origin>/auth/signinwin/zitadel`, `<app origin>/auth/signinsilent/zitadel` and
`<app origin>/auth/signinpop/zitadel`; post-logout URIs `<app origin>/` and
`<app origin>/auth/signoutpop/zitadel`; for `https://localhost:5820` and each deployed origin. Its client id and the project's resource id go in the variables above.

### Icons

The PWA icons live in `libs/assets/myko/content/icons` (`myko.svg` and what
`moon myko-app:generate-pwa-assets` makes from it); `assets-sync` links that folder to the app's
`public/icons`.
