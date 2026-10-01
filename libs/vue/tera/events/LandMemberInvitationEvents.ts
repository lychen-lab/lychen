import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landMemberInvitationPostSucceededEvent: EventBusKey<
  components['schemas']['LandMemberInvitation.jsonld-land_member_invitation.post_land_member_invitation.post.output']
> = Symbol('land-member-invitation-post-succeeded');
export const landMemberInvitationDeleteSucceededEvent: EventBusKey<null> = Symbol(
  'land-member-invitation-delete-succeeded',
);
export const landMemberInvitationPatchSucceededEvent: EventBusKey<
  components['schemas']['LandMemberInvitation.jsonld-land_member_invitation.patch_land_member_invitation.patch.output']
> = Symbol('land-member-invitation-patch-succeeded');
// The accept/refuse transitions expose no invitation field, so their output schemas are generated
// as `HydraItemBaseSchema & Record<string, never>`, which no response can satisfy. Their payload
// is the bare JSON-LD item.
export const landMemberInvitationAcceptSucceededEvent: EventBusKey<
  components['schemas']['HydraItemBaseSchema']
> = Symbol('land-member-invitation-accept-succeeded');
export const landMemberInvitationRefuseSucceededEvent: EventBusKey<
  components['schemas']['HydraItemBaseSchema']
> = Symbol('land-member-invitation-refuse-succeeded');
