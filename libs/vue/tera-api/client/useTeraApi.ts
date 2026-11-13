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

/**
 * The tera-api SDK, for this lib's endpoint composables only: the front calls those,
 * which speak its models, never the SDK and the API's representations directly.
 *
 * The components and pages that still call it directly (libs/vue/tera, tera-app) move to
 * endpoint composables one resource at a time, as endpoint/land/ shows.
 */
export function useTeraApi() {
  return { api };
}
