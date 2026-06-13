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
export const landMemberInvitationAcceptSucceededEvent: EventBusKey<
  components['schemas']['LandMemberInvitation.jsonld-land_member_invitation.accept_land_member_invitation.accept.output']
> = Symbol('land-member-invitation-accept-succeeded');
export const landMemberInvitationRefuseSucceededEvent: EventBusKey<
  components['schemas']['LandMemberInvitation.jsonld-land_member_invitation.refuse_land_member_invitation.refuse.output']
> = Symbol('land-member-invitation-refuse-succeeded');
