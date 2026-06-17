<template>
  <PageHomeHero />
  <PageHomeIntro id="discover" />
  <PageHomeEngage />
  <PageHomeMesh />
  <PageHomePeople />
  <Container wrapper-class="bg-gradient-to-b from-surface-container to-surface">
    <Title variant="h2">Gratuit. Fun. A ton rythme.</Title>
  </Container>
</template>

<script lang="ts" setup>
import ogImageUrl from './assets/HomeOgImage.webp';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { messages, TRANSLATION_KEY } from './i18n';
import PageHomeHero from './PageHomeHero.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import PageHomeIntro from './PageHomeIntro.vue';
import PageHomeMesh from './PageHomeMesh.vue';
import PageHomeEngage from './PageHomeEngage.vue';
import PageHomePeople from './PageHomePeople.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

useExtendedHead(t, { ogImage: ogImageUrl });
</script>

<style lang="css" scoped></style>
