import type {
  ApiLandsUlidPatchData,
  ApiLandsUlidPatchResponse,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type { Land, LandUpdateInput } from '@lychen/typescript-tera-models/Land';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';

export interface UpdateLandVariables {
  ulid: string;
  /** The fields to change; those left out are kept. */
  input: LandUpdateInput;
}

/** Changes a land, `PATCH /api/lands/{ulid}`, and resolves with it. */
export function useUpdateLand(options: ApiMutationOptions<Land, UpdateLandVariables> = {}) {
  const { api } = useTeraApi();

  return useApiMutation(
    {
      async send(variables: UpdateLandVariables) {
        const { data } = await api.apiLandsUlidPatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [landKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(variables: UpdateLandVariables): Omit<ApiLandsUlidPatchData, 'url'> {
  return {
    path: { ulid: variables.ulid },
    body: withoutUndefined({
      name: variables.input.name,
      surface: variables.input.surface,
      altitude: variables.input.altitude,
    }),
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(data: ApiLandsUlidPatchResponse): Land {
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
