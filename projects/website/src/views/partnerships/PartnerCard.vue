<template>
  <div
    class="bg-surface-container flex max-w-[500px] flex-col items-center justify-center gap-8 rounded-2xl p-4 md:flex-row"
  >
    <img
      v-if="image"
      :src="image"
      :alt="name"
      class="w-[100px] rounded-lg object-cover"
    />
    <div class="flex flex-col justify-center gap-2 text-center md:text-left">
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
