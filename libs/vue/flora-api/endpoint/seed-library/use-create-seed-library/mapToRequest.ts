import type { ApiSeedLibrariesPostData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibraryCreateInput } from '@lychen/typescript-flora-models/SeedLibrary';

/** The request, from the front's input, under the API's names. */
export function mapToRequest(input: SeedLibraryCreateInput): Omit<ApiSeedLibrariesPostData, 'url'> {
  return { body: withoutUndefined({ name: input.name, city: input.city }) };
}
