<template>
  <TagsInputInput
    v-bind="forwardedProps"
    :class="cn('text-sm min-h-6 flex-1 px-1 bg-transparent', props.class)"
  />
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { TagsInputInput, type TagsInputInputProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<TagsInputInputProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>
