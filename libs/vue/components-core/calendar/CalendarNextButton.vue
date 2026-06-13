<template>
  <CalendarNext
    data-slot="calendar-next-button"
    :class="
      cn(
        'absolute right-1',
        'size-7 bg-transparent p-0 opacity-50 hover:opacity-100 rounded-md cursor-pointer',
        props.class,
      )
    "
    v-bind="forwardedProps"
  >
    <slot>
      <IconChevronRight class="size-4" />
    </slot>
  </CalendarNext>
</template>

<script lang="ts" setup>
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { CalendarNext, type CalendarNextProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';
import IconChevronRight from '@lychen/vue-icons/IconChevronRight.vue';

const props = defineProps<CalendarNextProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>
