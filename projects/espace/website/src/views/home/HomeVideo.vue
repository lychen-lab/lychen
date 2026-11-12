<template>
  <div class="flex w-full flex-col gap-2">
    <div class="aspect-video w-full overflow-hidden rounded-xl">
      <iframe
        v-if="isPlaying"
        ref="player"
        class="size-full"
        :src="`https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`"
        :title
        allow="autoplay; encrypted-media; picture-in-picture; web-share"
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen
      ></iframe>
      <button
        v-else
        type="button"
        class="group relative size-full cursor-pointer"
        :aria-label="playLabel"
        @click="play"
      >
        <img
          :src="thumbnail"
          alt=""
          loading="lazy"
          class="size-full object-cover"
        />
        <span
          class="absolute top-1/2 left-1/2 flex size-16 -translate-1/2 items-center justify-center rounded-full bg-surface-container/80 text-on-surface backdrop-blur-md transition-transform group-hover:scale-110"
        >
          <svg
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
            class="size-8"
          >
            <path d="M8 5v14l11-7z" />
          </svg>
        </span>
      </button>
    </div>
    <p class="text-xs opacity-60">{{ notice }}</p>
  </div>
</template>

<script lang="ts" setup>
import { nextTick, ref, useTemplateRef } from 'vue';

// The video is only requested from YouTube once the visitor asks for it: until
// then the page shows a local thumbnail and contacts no third party.
defineProps<{
  videoId: string;
  title: string;
  thumbnail: string;
  playLabel: string;
  notice: string;
}>();

const isPlaying = ref(false);
const player = useTemplateRef('player');

async function play() {
  isPlaying.value = true;
  // The button that had the focus is gone: hand the focus over to the player.
  await nextTick();
  player.value?.focus();
}
</script>
