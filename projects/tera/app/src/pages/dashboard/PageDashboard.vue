<template>
  <section class="flex flex-col gap-6">
    <div class="flex flex-row items-center justify-between gap-4">
      <Title variant="h2"
        >Bonjour,
        <span class="text-on-surface/70">{{
          zitadelAuth.oidcAuth.userProfile.given_name
        }}</span></Title
      >
      <div class="flex flex-row items-center gap-2"></div>
    </div>
    <div
      :style="{ backgroundImage: `url(${headerImg})` }"
      class="flex min-h-50 flex-col justify-end rounded-3xl bg-cover bg-center bg-no-repeat p-8"
    ></div>

    <div
      class="-mt-16 flex flex-row items-center justify-between rounded-3xl bg-surface-container-low p-4 shadow-md"
    >
      <div class="flex flex-col gap-0">
        <Title variant="h5">Note rapide</Title>
        <small>Journalisez vos observations</small>
      </div>
      <div class="flex flex-row items-center gap-2">
        <Button variant="ghost">
          <template #icon><IconCamera /></template
        ></Button>
        <Button variant="ghost">
          <template #icon><IconMic /></template
        ></Button>
        <Button variant="ghost">
          <template #icon><IconKeyboard /></template
        ></Button>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import zitadelAuth from '@lychen/vue-zitadel/ZitadelAuth';
import { defineAsyncComponent } from 'vue';
import headerImg from './assets/header.webp';
import IconKeyboard from '@lychen/vue-icons/IconKeyboard.vue';
import IconMic from '@lychen/vue-icons/IconMic.vue';
import IconCamera from '@lychen/vue-icons/IconCamera.vue';

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Button = defineAsyncComponent(() => import('@lychen/vue-components-core/button/Button.vue'));
</script>

<style lang="css" scoped></style>
