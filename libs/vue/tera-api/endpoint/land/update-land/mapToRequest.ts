import type { ApiLandsUlidPatchData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { LandUpdateInput } from '@lychen/typescript-front-model-business/land/Land';

export interface UpdateLandVariables {
  id: string;
  /** The fields to change; those left out are kept. */
  input: LandUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  id,
  input,
}: UpdateLandVariables): Omit<ApiLandsUlidPatchData, 'url'> {
  return {
    path: { ulid: id },
    body: withoutUndefined({
      name: input.name,
      surface: input.surface,
      altitude: input.altitude,
    }),
  };
}
