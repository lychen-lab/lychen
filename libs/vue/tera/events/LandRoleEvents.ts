import type {
  LandRoleJsonld,
  LandRoleJsonldLandRolePatchLandRolePatchOutput,
  LandRoleJsonldLandRolePostLandRolePostOutput,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landRolePostSucceededEvent: EventBusKey<LandRoleJsonldLandRolePostLandRolePostOutput> =
  Symbol('land-role-post-succeeded');
export const landRoleDeleteSucceededEvent: EventBusKey<LandRoleJsonld> = Symbol(
  'land-role-delete-succeeded',
);
export const landRolePatchSucceededEvent: EventBusKey<LandRoleJsonldLandRolePatchLandRolePatchOutput> =
  Symbol('land-role-patch-succeeded');
