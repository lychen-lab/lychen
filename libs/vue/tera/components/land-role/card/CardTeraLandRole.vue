<template>
  <Card
    class="gap-2 p-4"
    hoverable
  >
    <p class="font-medium">{{ landRole.name }}</p>
    <p
      v-if="landRole.landMembers"
      class="text-sm opacity-70"
    >
      {{ t('property.land_members.default', landRole.landMembers?.length || 0) }}
    </p>
    <div class="flex flex-row flex-wrap gap-2">
      <BadgeTeraPermission
        v-for="permission in landRole.permissions"
        :key="permission"
        :permission="permission"
        class="bg-surface-container-high text-on-surface-container-high"
      />
    </div>
  </Card>
</template>

<script setup lang="ts">
import Card from '@lychen/vue-components-core/card/Card.vue';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-role';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import BadgeTeraPermission from '../../permission/badge/BadgeTeraPermission.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

defineProps<{ landRole: components['schemas']['LandRole.jsonld'] }>();
</script>
