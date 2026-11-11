import type { ApiLandsPostData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { LandCreateInput } from '@lychen/typescript-tera-models/Land';

/** The request, from the front's input, under the API's names. */
export function mapToRequest(input: LandCreateInput): Omit<ApiLandsPostData, 'url'> {
  return {
    body: withoutUndefined({
      name: input.name,
      surface: input.surface,
      altitude: input.altitude,
    }),
  };
}
