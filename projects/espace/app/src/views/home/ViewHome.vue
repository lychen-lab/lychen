<template>
  <section class="relative flex h-[400px] flex-col gap-4 rounded-4xl">
    <img
      src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1768572225/019bc71e-9a9d-7b3c-9478-f855ba60f20c.jpg"
      alt=""
      class="absolute z-2 h-full w-full rounded-4xl object-cover"
      loading="lazy"
      decoding="async"
    />
    <div class="z-3 flex h-full flex-col items-end justify-end gap-2 p-6">
      <h1 class="text-2xl font-bold text-white">Cultiver quelque chose ensemble</h1>
      <p class="text-md text-white opacity-80">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.
      </p>
    </div>
  </section>
  <section>
    <h2 class="text-xl font-bold">Comment voulez-vous commencer ?</h2>
    <div class="flex flex-row gap-2">
      <div class="rounded-xl bg-orange-500 p-4">J'ai un terrain à partager</div>
      <div class="rounded-xl bg-green-500 p-4">Je recherche un terrain</div>
    </div>
  </section>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">Comment ça marche</h2>
    <div>
      <h3>1. Découvre</h3>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
    </div>
    <div>
      <h3>2. Connecte</h3>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
    </div>
    <div>
      <h3>3. Cultive</h3>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
    </div>
  </section>
  <section>
    <div class="flex flex-row items-center justify-between">
      <h2 class="text-xl font-bold">Derniers terrains</h2>
      <Button>Voir tout</Button>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style lang="css" scoped></style>
