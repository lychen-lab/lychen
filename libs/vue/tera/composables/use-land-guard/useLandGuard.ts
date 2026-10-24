import type { LandMemberJsonldLandMemberMe } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { computed, type Ref } from 'vue';
import { LAND_GUARD_STRATEGY, type LandGuardStrategy } from '.';

export function useLandGuard(
  landMember: Ref<LandMemberJsonldLandMemberMe | undefined> | undefined,
  permissions: string[],
  strategy: LandGuardStrategy = LAND_GUARD_STRATEGY.Unanimous,
) {
  const allowed = computed(() => {
    if (!landMember || !landMember.value) {
      return false;
    }

    if (landMember.value.owner) {
      return true;
    }

    if (!landMember.value.landRoles) {
      return false;
    }

    // Each role's `permissions` is an array of permission strings at runtime, but the generated SDK
    // types it as a scalar enum union (the spec declares the enum on the array itself, without
    // `items`). Read it as a string array so it can be flattened and compared against the
    // requested `permissions`.
    const allPermissions = landMember.value.landRoles.flatMap(
      (role) => (role.permissions as unknown as string[] | undefined) ?? [],
    );
    if (strategy === 'unanimous') {
      return permissions.every((permission) => allPermissions.includes(permission));
    } else if (strategy === 'affirmative') {
      return permissions.some((permission) => allPermissions.includes(permission));
    }
    return false;
  });

  return { allowed };
}
