import { defineApiSdkConfig } from '@lychen/typescript-api-sdk-config/defineApiSdkConfig';

export default defineApiSdkConfig({
  api: 'espace',
  // The spec espace-api:export-openapi writes.
  input: '../../../projects/espace/api/openapi_docs.json',
});
