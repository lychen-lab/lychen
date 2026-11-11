import { defineConfig } from '@hey-api/openapi-ts';

// The single @hey-api/openapi-ts configuration of the API SDKs (libs/typescript/<api>-api-sdk),
// so that every SDK exposes the same client. An SDK's openapi-ts.config.ts only names its API
// and its spec:
//
//   export default defineApiSdkConfig({
//     api: 'tera',
//     input: '../../../projects/tera/api/openapi_docs.json',
//   });
//
// The CLI resolves relative paths against the working directory, which is the SDK library for
// its moon tasks (generate-api, generate-api-from-url). `openapi-ts --input <url>`, which
// generate-api-from-url runs, replaces the input and keeps the rest of this configuration.

export interface ApiSdkOptions {
  /**
   * The API's name, in kebab-case (`tera`). The SDK is written to `generated/<api>-api/` and
   * its operations are the methods of one `<Api>Api` class (`TeraApi`).
   */
  api: string;
  /**
   * The OpenAPI spec: a path relative to the SDK library (the file a monorepo API's
   * export-openapi writes) or a URL (an API outside the monorepo).
   */
  input: string;
}

const KEBAB_CASE = /^[a-z][a-z0-9]*(?:-[a-z0-9]+)*$/;

export function defineApiSdkConfig({ api, input }: ApiSdkOptions) {
  if (!KEBAB_CASE.test(api)) {
    throw new Error(`defineApiSdkConfig: api must be kebab-case (e.g. "tera"), got "${api}".`);
  }

  return defineConfig({
    input,
    output: `generated/${api}-api`,
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
        operations: {
          strategy: 'single',
          containerName: `${pascalCase(api)}Api`,
          methods: 'instance',
        },
      },
      '@hey-api/client-fetch',
    ],
  });
}

function pascalCase(kebab: string): string {
  return kebab
    .split('-')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join('');
}
