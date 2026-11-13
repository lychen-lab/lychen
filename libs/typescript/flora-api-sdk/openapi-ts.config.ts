import { defineApiSdkConfig } from '@lychen/typescript-api-sdk-config/defineApiSdkConfig';

export default defineApiSdkConfig({
  api: 'flora',
  // The spec flora-api:export-openapi writes.
  input: '../../../projects/flora/api/openapi_docs.json',
});
