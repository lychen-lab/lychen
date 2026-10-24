import type {
  HydraItemBaseSchema,
  LandMemberInvitationJsonldLandMemberInvitationPatchLandMemberInvitationPatchOutput,
  LandMemberInvitationJsonldLandMemberInvitationPostLandMemberInvitationPostOutput,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landMemberInvitationPostSucceededEvent: EventBusKey<LandMemberInvitationJsonldLandMemberInvitationPostLandMemberInvitationPostOutput> =
  Symbol('land-member-invitation-post-succeeded');
export const landMemberInvitationDeleteSucceededEvent: EventBusKey<null> = Symbol(
  'land-member-invitation-delete-succeeded',
);
export const landMemberInvitationPatchSucceededEvent: EventBusKey<LandMemberInvitationJsonldLandMemberInvitationPatchLandMemberInvitationPatchOutput> =
  Symbol('land-member-invitation-patch-succeeded');
// The accept/refuse transitions expose no invitation field, so their output schemas are generated
// as `HydraItemBaseSchema & Record<string, never>`, which no response can satisfy. Their payload
// is the bare JSON-LD item.
export const landMemberInvitationAcceptSucceededEvent: EventBusKey<HydraItemBaseSchema> = Symbol(
  'land-member-invitation-accept-succeeded',
);
export const landMemberInvitationRefuseSucceededEvent: EventBusKey<HydraItemBaseSchema> = Symbol(
  'land-member-invitation-refuse-succeeded',
);
