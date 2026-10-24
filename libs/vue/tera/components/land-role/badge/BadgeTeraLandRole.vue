<template>
  <Badge class="bg-tertiary-container text-on-tertiary-container">
    {{ landRole.name }}
  </Badge>
</template>

<script setup lang="ts">
import type { LandRoleJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import Badge from '@lychen/vue-components-core/badge/Badge.vue';

defineProps<{ landRole: Pick<LandRoleJsonld, 'name'> }>();
</script>
