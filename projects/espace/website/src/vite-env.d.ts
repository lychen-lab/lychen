/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_ID: string;
  readonly VITE_UNHEAD_HOST: string;
  readonly VITE_ESPACE_APP_HOST: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
