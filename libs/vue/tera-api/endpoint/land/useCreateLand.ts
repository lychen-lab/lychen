import type {
  ApiLandsPostData,
  ApiLandsPostResponse,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type { Land, LandCreateInput } from '@lychen/typescript-tera-models/Land';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';

/** What the creation form gives. */
export type CreateLandVariables = LandCreateInput;

/** Creates a land, `POST /api/lands`, and resolves with it. */
export function useCreateLand(options: ApiMutationOptions<Land, CreateLandVariables> = {}) {
  const { api } = useTeraApi();

  return useApiMutation(
    {
      async send(variables: CreateLandVariables) {
        const { data } = await api.apiLandsPost({ ...mapToRequest(variables), throwOnError: true });
        return mapToModel(data);
      },
      invalidates: () => [landKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(variables: CreateLandVariables): Omit<ApiLandsPostData, 'url'> {
  return {
    body: withoutUndefined({
      name: variables.name,
      surface: variables.surface,
      altitude: variables.altitude,
    }),
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(data: ApiLandsPostResponse): Land {
  return {
    ulid: required(data.ulid, 'land ulid'),
    name: data.name ?? '',
    surface: optional(data.surface),
    altitude: optional(data.altitude),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
    // landMembers, landAreas and landSetting are IRIs of other resources: left out until a
    // screen needs them, then mapped to their ulids.
  };
}
