import { defineApiSdkConfig } from '@lychen/typescript-api-sdk-config/defineApiSdkConfig';

export default defineApiSdkConfig({
  api: 'tera',
  // The spec tera-api:export-openapi writes.
  input: '../../../projects/tera/api/openapi_docs.json',
});
