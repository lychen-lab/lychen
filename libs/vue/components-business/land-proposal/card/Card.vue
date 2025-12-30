<template>
  <div
    class="bg-surface-container-low border-on-surface/5 max-w-[300px] rounded-3xl border-1 p-2"
    :data-uuid="uuid"
    :class="clickable ? 'cursor-pointer hover:shadow-md' : 'cursor-default'"
  >
    <div class="relative aspect-[1/1] overflow-hidden rounded-2xl">
      <img
        :src="image"
        :alt="title"
        class="h-full w-full object-cover"
        loading="lazy"
        decoding="async"
      />
      <Button
        class="bg-surface/5 absolute top-2 right-2 backdrop-blur-sm"
        icon-only
        size="sm"
        @click="$emit('favorite')"
        ><template #icon
          ><IconHeart v-if="!isFavorite" /><IconHeartFilled
            v-else
            class="text-red-700" /></template
      ></Button>
      <div
        v-if="displayCity && city"
        class="absolute bottom-4 left-4 flex items-center gap-1 rounded-lg bg-lime-50 px-2 py-0.5 text-[0.8rem] text-lime-800"
      >
        <IconMapPin class="!size-4" /> {{ city }}
      </div>
    </div>
    <div class="flex flex-col gap-4 p-4">
      <div class="flex flex-col gap-1">
        <h3 class="font-bold">{{ title }}</h3>
        <p class="line-clamp-3 text-sm opacity-70">{{ description }}</p>
      </div>
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 text-xs">
          <div class="flex items-center gap-1">
            <IconSquareDashed class="!size-4 text-lime-700 dark:text-lime-200" />{{
              n(surface, 'square-meter')
            }}
          </div>
          <div class="flex items-center gap-1">
            <IconMountain class="!size-4 text-lime-700 dark:text-lime-200" />{{
              n(altitude, 'meter')
            }}
          </div>
        </div>
        <Button
          size="sm"
          class="text-lime-700 dark:text-lime-200"
          variant="ghost"
          >Détails <IconArrowRight
        /></Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { LandProposal } from '.';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import IconHeart from '@lychen/vue-icons/IconHeart.vue';
import IconHeartFilled from '@lychen/vue-icons/IconHeartFilled.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import IconSquareDashed from '@lychen/vue-icons/IconSquareDashed.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

interface Props extends LandProposal {
  isFavorite?: boolean;
  clickable?: boolean;
  displayCity?: boolean;
}

defineProps<Props>();

interface Events {
  (e: 'click'): void;
  (e: 'favorite'): void;
}
defineEmits<Events>();

const { n } = usePrefixedI18n();
</script>
