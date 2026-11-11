import { TeraApi } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { createClient } from '@lychen/typescript-tera-api-sdk/generated/tera-api/client';
import { createApiClient } from '@lychen/typescript-api-client/createApiClient';
import { getAccessToken } from '@lychen/vue-zitadel/getAccessToken';

// Set up like every API's client (libs/typescript/api-client), with the signed-in
// user's token: pass `interceptors` to change tera's own.
const api = new TeraApi({
  client: createApiClient(createClient, {
    baseUrl: import.meta.env.VITE_API_URL,
    bearerToken: getAccessToken,
  }),
});

export function useTeraApi() {
  return { api };
}
