import type {
  LandJsonld,
  LandJsonldLandPatchLandPatchOutput,
  LandJsonldLandPostLandPostOutput,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landPostSucceededEvent: EventBusKey<LandJsonldLandPostLandPostOutput> =
  Symbol('land-post-succeeded');
export const landDeleteSucceededEvent: EventBusKey<LandJsonld> = Symbol('land-delete-succeeded');
export const landPatchSucceededEvent: EventBusKey<LandJsonldLandPatchLandPatchOutput> =
  Symbol('land-patch-succeeded');
