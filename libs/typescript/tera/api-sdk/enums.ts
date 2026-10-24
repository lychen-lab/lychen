import type {
  LandMemberInvitationJsonldLandMemberInvitationCollection,
  LandRoleJsonld,
  LandTaskJsonld,
} from './generated/tera-api';

// Runtime values of the enums the frontends list or compare against. @hey-api/openapi-ts
// only emits runtime enums for enum components, and API Platform inlines these ones in
// the resource schemas, so they are written out here. `satisfies` holds each object to
// exactly the values of the generated type: once the SDK is regenerated, a value the API
// added or removed fails the type-check here.

export type LandTaskState = NonNullable<LandTaskJsonld['state']>;
export const LandTaskState = {
  to_be_done: 'to_be_done',
  in_progress: 'in_progress',
  done: 'done',
} as const satisfies { [K in LandTaskState]: K };

export type LandMemberInvitationState = NonNullable<
  LandMemberInvitationJsonldLandMemberInvitationCollection['state']
>;
export const LandMemberInvitationState = {
  pending: 'pending',
  accepted: 'accepted',
  refused: 'refused',
} as const satisfies { [K in LandMemberInvitationState]: K };

// The spec declares the permission enum on the `permissions` array itself (no `items`),
// so the generated type is a single permission although the API sends a list of them.
export type LandRolePermission = NonNullable<LandRoleJsonld['permissions']>;
export const LandRolePermission = {
  'land_member:land:delete': 'land_member:land:delete',
  'land_member:land:patch': 'land_member:land:patch',
  'person-land_member:land:get': 'person-land_member:land:get',
  'land_member:land_member:delete': 'land_member:land_member:delete',
  'land_member:land_member:patch': 'land_member:land_member:patch',
  'land_member:land_member:get': 'land_member:land_member:get',
  'land_member:land_member:collection': 'land_member:land_member:collection',
  'land_member:land_task:delete': 'land_member:land_task:delete',
  'land_member:land_task:post': 'land_member:land_task:post',
  'land_member:land_task:patch': 'land_member:land_task:patch',
  'land_member:land_task:get': 'land_member:land_task:get',
  'land_member:land_task:collection': 'land_member:land_task:collection',
  'land_member:land_task:mark_as_done': 'land_member:land_task:mark_as_done',
  'land_member:land_task:mark_as_in_progress': 'land_member:land_task:mark_as_in_progress',
  'land_member:land_area:delete': 'land_member:land_area:delete',
  'land_member:land_area:post': 'land_member:land_area:post',
  'land_member:land_area:patch': 'land_member:land_area:patch',
  'land_member:land_area:get': 'land_member:land_area:get',
  'land_member:land_area:collection': 'land_member:land_area:collection',
  'land_member:land_area_setting:patch': 'land_member:land_area_setting:patch',
  'land_member:land_area_setting:get': 'land_member:land_area_setting:get',
  'land_member:land_area_parameter:patch': 'land_member:land_area_parameter:patch',
  'land_member:land_area_parameter:get': 'land_member:land_area_parameter:get',
  'land_member:land_setting:patch': 'land_member:land_setting:patch',
  'land_member:land_setting:get': 'land_member:land_setting:get',
  'land_member:land_greenhouse:delete': 'land_member:land_greenhouse:delete',
  'land_member:land_greenhouse:post': 'land_member:land_greenhouse:post',
  'land_member:land_greenhouse:patch': 'land_member:land_greenhouse:patch',
  'land_member:land_greenhouse:get': 'land_member:land_greenhouse:get',
  'land_member:land_greenhouse:collection': 'land_member:land_greenhouse:collection',
  'land_member:land_greenhouse_setting:patch': 'land_member:land_greenhouse_setting:patch',
  'land_member:land_greenhouse_setting:get': 'land_member:land_greenhouse_setting:get',
  'land_member:land_greenhouse_parameter:patch': 'land_member:land_greenhouse_parameter:patch',
  'land_member:land_greenhouse_parameter:get': 'land_member:land_greenhouse_parameter:get',
  'land_member:land_cultivation_plan:delete': 'land_member:land_cultivation_plan:delete',
  'land_member:land_cultivation_plan:post': 'land_member:land_cultivation_plan:post',
  'land_member:land_cultivation_plan:patch': 'land_member:land_cultivation_plan:patch',
  'land_member:land_cultivation_plan:get': 'land_member:land_cultivation_plan:get',
  'land_member:land_cultivation_plan:collection': 'land_member:land_cultivation_plan:collection',
  'land_member:land_member_invitation:delete': 'land_member:land_member_invitation:delete',
  'land_member:land_member_invitation:patch': 'land_member:land_member_invitation:patch',
  'land_member:land_member_invitation:post': 'land_member:land_member_invitation:post',
  'land_member:land_member_invitation:get': 'land_member:land_member_invitation:get',
  'land_member:land_member_invitation:collection': 'land_member:land_member_invitation:collection',
  'land_member:land_member_invitation:check_email_unicity':
    'land_member:land_member_invitation:check_email_unicity',
  'land_member:land_api_key:delete': 'land_member:land_api_key:delete',
  'land_member:land_api_key:post': 'land_member:land_api_key:post',
  'land_member:land_api_key:get': 'land_member:land_api_key:get',
  'land_member:land_api_key:collection': 'land_member:land_api_key:collection',
  'land_member:land_proposal:post': 'land_member:land_proposal:post',
  'person-land_member:land_proposal:get': 'person-land_member:land_proposal:get',
  'land_member:land_proposal:patch': 'land_member:land_proposal:patch',
  'land_member:land_proposal:delete': 'land_member:land_proposal:delete',
  'land_member:land_proposal:publish': 'land_member:land_proposal:publish',
  'land_member:land_proposal:archive': 'land_member:land_proposal:archive',
  'land_member:land_proposal:collection': 'land_member:land_proposal:collection',
  'land_member:land_harvest_entry:delete': 'land_member:land_harvest_entry:delete',
  'land_member:land_harvest_entry:post': 'land_member:land_harvest_entry:post',
  'land_member:land_harvest_entry:patch': 'land_member:land_harvest_entry:patch',
  'land_member:land_harvest_entry:get': 'land_member:land_harvest_entry:get',
  'land_member:land_harvest_entry:collection': 'land_member:land_harvest_entry:collection',
} as const satisfies { [K in LandRolePermission]: K };
