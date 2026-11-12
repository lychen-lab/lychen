import { EspaceApi } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createClient } from '@lychen/typescript-espace-api-sdk/generated/espace-api/client';
import { createApiClient } from '@lychen/typescript-api-client/createApiClient';
import { getAccessToken } from '@lychen/vue-zitadel/getAccessToken';

// Set up like every API's client (libs/typescript/api-client), with the signed-in
// user's token: pass `interceptors` to change espace's own.
const api = new EspaceApi({
  client: createApiClient(createClient, {
    baseUrl: import.meta.env.VITE_ESPACE_API_HOST,
    bearerToken: getAccessToken,
  }),
});

/**
 * The espace-api SDK, for this lib's endpoint composables only: the front calls those,
 * which speak its models, never the SDK and the API's representations directly.
 */
export function useEspaceApi() {
  return { api };
}
