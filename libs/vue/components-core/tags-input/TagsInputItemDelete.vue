<template>
  <TagsInputItemDelete
    v-bind="forwardedProps"
    :class="cn('flex rounded bg-transparent mr-1 cursor-pointer', props.class)"
  >
    <slot>
      <IconTimes class="h-4 w-4" />
    </slot>
  </TagsInputItemDelete>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import IconTimes from '@lychen/vue-icons/IconTimes.vue';
import { TagsInputItemDelete, type TagsInputItemDeleteProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<TagsInputItemDeleteProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>
