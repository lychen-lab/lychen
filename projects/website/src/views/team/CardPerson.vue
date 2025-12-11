<template>
  <div class="flex flex-col gap-4">
    <div class="size-20 rounded-full bg-gray-300">
      <img
        v-if="image"
        :src="image"
      />
    </div>
    <div class="flex flex-col gap-1">
      <h3 class="font-bold">{{ firstname }} {{ lastname }}</h3>
      <small
        v-if="role"
        class="text-green-700 opacity-60"
        >{{ role }}</small
      >
      <p class="text-xs opacity-60">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis quos alias laudantium
        deleniti perspiciatis nesciunt doloribus.
      </p>
    </div>
    <a
      v-if="link"
      :href="link"
      class="opacity-60"
    >
      <IconLink />
    </a>
  </div>
</template>

<script setup lang="ts">
import IconLink from '@lychen/vue-icons/IconLink.vue';

interface Props {
  firstname: string;
  lastname?: string;
  image?: string;
  role?: string;
  link?: string;
}

defineProps<Props>();
</script>
