import type { ApiLandsUlidPatchData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { LandUpdateInput } from '@lychen/typescript-tera-models/Land';

export interface UpdateLandVariables {
  ulid: string;
  /** The fields to change; those left out are kept. */
  input: LandUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  ulid,
  input,
}: UpdateLandVariables): Omit<ApiLandsUlidPatchData, 'url'> {
  return {
    path: { ulid },
    body: withoutUndefined({
      name: input.name,
      surface: input.surface,
      altitude: input.altitude,
    }),
  };
}
