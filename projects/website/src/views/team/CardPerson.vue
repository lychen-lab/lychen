<template>
  <div
    class="group relative flex aspect-3/4 w-full flex-col overflow-hidden rounded-[32px] bg-stone-100 shadow-md transition-all duration-300 hover:shadow-xl"
  >
    <!-- Image Background -->
    <img
      v-if="image"
      :src="image"
      class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
      :alt="`${firstname} ${lastname}`"
    />
    <div
      v-else
      class="absolute inset-0 h-full w-full bg-stone-300"
    ></div>

    <!-- Gradient Overlay -->
    <div
      class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent opacity-80 transition-opacity duration-300 group-hover:opacity-90"
    ></div>

    <!-- Top Badge (Role) -->
    <div
      v-if="role"
      class="absolute top-4 left-4"
    >
      <div
        class="rounded-full border border-white/10 bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur-md"
      >
        {{ t('roles.' + role) }}
      </div>
    </div>

    <!-- Bottom Content -->
    <div class="absolute right-0 bottom-0 left-0 flex flex-col gap-2 p-6 text-white">
      <div class="flex items-center gap-2">
        <h3 class="text-xl leading-tight font-bold">{{ firstname }} {{ lastname }}</h3>
      </div>

      <p class="line-clamp-3 text-sm text-gray-200">
        {{ t(`member_card.bios.${id}`) }}
      </p>

      <div class="mt-2 flex items-center justify-between">
        <div class="flex flex-col">
          <small class="text-xs text-gray-300">{{ email }}</small>
        </div>

        <a
          v-if="link"
          :href="link"
          target="_blank"
          rel="noopener noreferrer"
          class="rounded-full bg-white px-4 py-2 text-xs font-bold text-black transition-transform hover:scale-105 active:scale-95"
        >
          Follow +
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
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
