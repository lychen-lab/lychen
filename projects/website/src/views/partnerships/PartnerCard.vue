<template>
  <div class="bg-surface-container flex flex-row items-center gap-8 rounded-2xl p-4">
    <div class="flex h-[100px] w-[100px] flex-col items-center justify-center">
      <img
        v-if="image"
        :src="image"
        :alt="name"
        class="w-full rounded-lg object-cover"
      />
    </div>
    <div class="flex flex-col gap-2">
      <Title variant="h3">{{ name }}</Title>
      <Paragraph
        v-if="description"
        class="opacity-80"
        >{{ description }}</Paragraph
      >
      <a
        v-if="link"
        :href="link"
        target="_blank"
        ><Button size="sm">
          Site web <template #icon><IconArrowUpRight /></template></Button
      ></a>
    </div>
  </div>
</template>

<script setup lang="ts">
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';

interface Props {
  link?: string;
  name?: string;
  description?: string;
  image?: string;
}

const props = defineProps<Props>();
</script>
