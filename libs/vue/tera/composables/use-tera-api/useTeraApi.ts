import { TeraApi } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { createClient } from '@lychen/typescript-tera-api-sdk/generated/tera-api/client';
import { createApiClient } from '@lychen/vue-auth/createApiClient';

// Set up like every API's client (libs/vue/auth): pass `interceptors` to change tera's own.
const api = new TeraApi({
  client: createApiClient(createClient, { baseUrl: import.meta.env.VITE_API_URL }),
});

export function useTeraApi() {
  return { api };
}
