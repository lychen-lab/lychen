<template>
  <div class="flex flex-col gap-4">
    <div class="size-30 rounded-full bg-stone-200">
      <img
        v-if="image"
        :src="image"
        class="rounded-full"
        :alt="`${firstname} ${lastname}`"
      />
    </div>
    <div class="flex flex-col gap-1">
      <h3 class="font-bold">{{ firstname }} {{ lastname }}</h3>
      <small
        v-if="role"
        class="text-green-700 opacity-60"
        >{{ t('roles.' + role) }}
      </small>
      <p class="text-xs opacity-60">
        {{ t(`member_card.bios.${id}`) }}
      </p>
      <small class="opacity-80">{{ email }}</small>
    </div>
    <a
      v-if="link"
      :href="link"
      class="opacity-60"
      :aria-label="t('visit_profile', { name: `${firstname} ${lastname}` })"
      target="_blank"
      rel="noopener noreferrer"
    >
      <IconLink />
    </a>
  </div>
</template>

<script setup lang="ts">
import IconLink from '@lychen/vue-icons/IconLink.vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';

const { t } = usePrefixedI18n(CONFIG);

interface Props {
  id: string;
  firstname: string;
  lastname?: string;
  image?: string;
  role?: string;
  link?: string;
  email?: string;
}

defineProps<Props>();
</script>
