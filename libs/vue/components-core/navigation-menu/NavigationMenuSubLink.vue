<template>
  <component
    :is="route ? 'RouterLink' : 'a'"
    :to="route ? { name: route.name } : null"
    class="focus:bg-primary-container/30 focus:text-on-primary-container flex cursor-pointer flex-col gap-1 rounded-md p-3 leading-none no-underline transition-colors outline-none select-none"
    :href="link"
    :target="link ? target : '_self'"
    @click="emitCloseIfRoute()"
  >
    <div class="no-wrap flex flex-row justify-between">
      <p class="text-md font-lexend leading-none font-black tracking-wide">
        {{ title }}
      </p>
      <div class="text-xs">
        <IconArrowUpRight
          v-if="link"
          class="opacity-60"
        />
      </div>
    </div>
    <p class="line-clamp-3 text-xs leading-snug opacity-70">
      {{ description }}
    </p>
  </component>
</template>

<script lang="ts" setup>
import { EVENT_NavigateToRoute } from '.';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';

interface Props {
  title: string;
  description: string;
  route?: { name: string };
  link?: string;
  target?: string;
}
const props = withDefaults(defineProps<Props>(), {
  target: '_blank',
  link: undefined,
  route: undefined,
});

const emit = defineEmits<{ (e: typeof EVENT_NavigateToRoute): void }>();
function emitCloseIfRoute() {
  if (props.route) {
    emit(EVENT_NavigateToRoute);
  }
}
</script>
