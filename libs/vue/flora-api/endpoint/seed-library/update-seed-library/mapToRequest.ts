import type { ApiSeedLibrariesUuidPatchData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibraryUpdateInput } from '@lychen/typescript-flora-models/SeedLibrary';

export interface UpdateSeedLibraryVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: SeedLibraryUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  uuid,
  input,
}: UpdateSeedLibraryVariables): Omit<ApiSeedLibrariesUuidPatchData, 'url'> {
  return { path: { uuid }, body: withoutUndefined({ name: input.name, city: input.city }) };
}
