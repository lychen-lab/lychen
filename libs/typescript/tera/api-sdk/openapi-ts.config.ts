import { defineConfig } from '@hey-api/openapi-ts';

// Paths are relative to this package: the CLI resolves them against the working
// directory, which is this package for both generate-api and the CI drift check.
export default defineConfig({
  input: '../../../../projects/tera/api/openapi_docs.json',
  output: 'generated/tera-api',
  plugins: [
    '@hey-api/typescript',
    // One class holding every operation, so each app binds it to its own configured
    // client (base URL, auth) instead of mutating the shared default one.
    {
      name: '@hey-api/sdk',
      operations: { strategy: 'single', containerName: 'TeraApi', methods: 'instance' },
    },
    '@hey-api/client-fetch',
  ],
});
