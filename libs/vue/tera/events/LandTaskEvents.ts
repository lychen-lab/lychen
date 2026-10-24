import type {
  LandTaskJsonld,
  LandTaskJsonldLandTaskPatchLandTaskPatchOutput,
  LandTaskJsonldLandTaskPostLandTaskPostOutput,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const EVENT_landTaskPostSucceeded: EventBusKey<LandTaskJsonldLandTaskPostLandTaskPostOutput> =
  Symbol();
export const EVENT_landTaskDeleteSucceeded: EventBusKey<LandTaskJsonld> = Symbol();
export const EVENT_landTaskPatchSucceeded: EventBusKey<LandTaskJsonldLandTaskPatchLandTaskPatchOutput> =
  Symbol();
