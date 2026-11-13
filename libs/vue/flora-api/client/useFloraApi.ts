import { FloraApi } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { createClient } from '@lychen/typescript-flora-api-sdk/generated/flora-api/client';
import { createApiClient } from '@lychen/typescript-api-client/createApiClient';
import { getAccessToken } from '@lychen/vue-zitadel/getAccessToken';

// Set up like every API's client (libs/typescript/api-client), with the signed-in
// user's token: pass `interceptors` to change flora's own.
const api = new FloraApi({
  client: createApiClient(createClient, {
    baseUrl: import.meta.env.VITE_FLORA_API_URL,
    bearerToken: getAccessToken,
  }),
});

/**
 * The flora-api SDK, for this lib's endpoint composables only: the front calls those,
 * which speak its models, never the SDK and the API's representations directly.
 */
export function useFloraApi() {
  return { api };
}
