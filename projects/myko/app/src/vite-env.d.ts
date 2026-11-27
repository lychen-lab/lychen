/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_ID: string;
  readonly VITE_MYKO_API_HOST: string;
  /** Development only, see src/auth/createAuth.ts: never set in a production build. */
  readonly VITE_MYKO_FAKE_AUTH?: string;
  readonly VITE_ZITADEL_CLIENT_ID: string;
  readonly VITE_ZITADEL_ISSUER: string;
  readonly VITE_ZITADEL_PROJECT_RESOURCE_ID: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
