<template>
  <CalendarPrev
    data-slot="calendar-prev-button"
    :class="
      cn(
        'absolute left-1',
        'size-7 bg-transparent p-0 opacity-50 hover:opacity-100 rounded-md cursor-pointer',
        props.class,
      )
    "
    v-bind="forwardedProps"
  >
    <slot>
      <IconChevronLeft class="size-4" />
    </slot>
  </CalendarPrev>
</template>

<script lang="ts" setup>
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { CalendarPrev, type CalendarPrevProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';
import IconChevronLeft from '@lychen/vue-icons/IconChevronLeft.vue';

const props = defineProps<CalendarPrevProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>
