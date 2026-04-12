/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_ID: string;
  readonly VITE_ESPACE_API_HOST: string;
  readonly VITE_ZITADEL_CLIENT_ID: string;
  readonly VITE_ZITADEL_ISSUER: string;
  readonly VITE_ZITADEL_PROJECT_RESOURCE_ID: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
