/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_ID: string;
  readonly VITE_UNHEAD_HOST: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
