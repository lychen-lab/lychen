import { defineConfig } from '@hey-api/openapi-ts';

// Paths are relative to this package: the CLI resolves them against the working
// directory, which is this package for both generate-api and the CI drift check.
export default defineConfig({
  input: '../../../projects/espace/api/openapi_docs.json',
  output: 'generated/espace-api',
  parser: {
    patch: {
      // API Platform documents its array filters (`city[]`, `place[]`, ...) with
      // `style: deepObject`, which OpenAPI only defines for objects and which the generated
      // client's types reject. The API reads them as exploded `city[]=a&city[]=b`: form.
      operations: (_method, _path, operation) => {
        const parameters = (operation.parameters ?? []) as {
          style?: string;
          schema?: { type?: unknown };
        }[];
        for (const parameter of parameters) {
          if (parameter.style === 'deepObject' && parameter.schema?.type === 'array') {
            parameter.style = 'form';
          }
        }
      },
    },
  },
  plugins: [
    '@hey-api/typescript',
    // One class holding every operation, so each app binds it to its own configured
    // client (base URL, auth) instead of mutating the shared default one.
    {
      name: '@hey-api/sdk',
      operations: { strategy: 'single', containerName: 'EspaceApi', methods: 'instance' },
    },
    '@hey-api/client-fetch',
  ],
});
