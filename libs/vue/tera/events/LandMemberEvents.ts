import type {
  LandMemberJsonld,
  LandMemberJsonldLandMemberPatchLandMemberPatchOutput,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landMemberPostSucceededEvent: EventBusKey<LandMemberJsonld> = Symbol(
  'land-member-post-succeeded',
);
export const landMemberDeleteSucceededEvent: EventBusKey<LandMemberJsonld> = Symbol(
  'land-member-delete-succeeded',
);
export const landMemberPatchSucceededEvent: EventBusKey<LandMemberJsonldLandMemberPatchLandMemberPatchOutput> =
  Symbol('land-member-patch-succeeded');
export const landMemberLeaveSucceededEvent: EventBusKey<LandMemberJsonld> = Symbol(
  'land-member-leave-succeeded',
);
