import { EspaceApi } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createClient } from '@lychen/typescript-espace-api-sdk/generated/espace-api/client';
import { createApiClient } from '@lychen/vue-auth/createApiClient';

// Set up like every API's client (libs/vue/auth): pass `interceptors` to change espace's own.
const api = new EspaceApi({
  client: createApiClient(createClient, { baseUrl: import.meta.env.VITE_ESPACE_API_HOST }),
});

export function useEspaceApi() {
  return { api };
}
