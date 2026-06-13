<template>
  <header class="flex flex-row items-center justify-between">
    <div class="font-lexend flex flex-row items-center gap-0">
      <span class="text-on-surface font-medium tracking-normal">espace</span>
      <span class="text-on-surface/40">.lychen</span>
    </div>
    <div class="bg-primary size-8 rounded-full"></div>
  </header>
  <section class="relative flex h-[500px] flex-col gap-4 rounded-4xl">
    <img
      src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1768572225/019bc71e-9a9d-7b3c-9478-f855ba60f20c.jpg"
      alt=""
      class="absolute z-2 h-full w-full rounded-4xl object-cover"
      loading="lazy"
      decoding="async"
    />
    <div class="z-3 flex h-full flex-col items-end justify-end gap-2 p-6">
      <h1 class="text-4xl font-bold text-white">Cultiver quelque chose de beau ensemble.</h1>
      <p class="text-md text-white opacity-80">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.
      </p>
    </div>
  </section>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">Comment voulez-vous commencer ?</h2>
    <div class="flex flex-row gap-2">
      <div class="flex flex-col gap-10 rounded-2xl bg-amber-700 p-6 text-amber-50">
        <IconFence />
        <div>
          <p class="text-lg leading-5 font-bold">I have space to share</p>
          <small class="text-xs opacity-70">Host a grower</small>
        </div>
      </div>
      <div class="flex flex-col gap-10 rounded-2xl bg-emerald-700 p-6 text-emerald-50">
        <IconSearch />
        <div>
          <p class="text-lg leading-5 font-bold">I'm looking for a space</p>
          <small class="text-xs opacity-70">Start growing</small>
        </div>
      </div>
    </div>
  </section>
  <section class="flex flex-col items-center gap-6">
    <h2 class="text-xl font-bold">Comment ça marche</h2>
    <SectionLuk
      title="1. Découvre"
      description="Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod."
    >
      <template #icon>
        <IconCompass />
      </template>
    </SectionLuk>
    <SectionLuk
      title="2. Connecte"
      description="Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod."
    >
      <template #icon>
        <IconHeartHandshake />
      </template>
    </SectionLuk>
    <SectionLuk
      title="3. Cultive"
      description="Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod."
    >
      <template #icon>
        <IconSprout />
      </template>
    </SectionLuk>
  </section>
  <section>
    <div class="flex flex-row items-center justify-between">
      <h2 class="text-xl font-bold">Derniers terrains</h2>
      <Button>Voir tout</Button>
    </div>
    <div class="flex flex-row gap-4 overflow-x-scroll py-2">
      <RouterLink
        v-for="proposal in fakeLandProposals"
        :key="proposal.uuid"
        :to="{ name: ROUTE_LAND_PROPOSAL.name, params: { uuid: proposal.uuid } }"
      >
        <LandProposalCard v-bind="proposal" />
      </RouterLink>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import IconCompass from '@lychen/vue-icons/IconCompass.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import SectionLuk from '@/views/home/SectionLuk.vue';
import { Card as LandProposalCard } from '@lychen/vue-components-business/land-proposal/card';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';

useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const fakeLandProposals = [
  {
    uuid: '1',
    title: 'Terrain de culture',
    description:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante feugiat finibus. Nullam nec metus vel ante feugiat finibus.',
    surface: 130,
    altitude: 678,
    city: 'Lille',
    image: 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
  },
  {
    uuid: '2',
    title: 'Cave à champignons',
    description:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante feugiat finibus. Nullam nec metus vel ante feugiat finibus.',
    surface: 40,
    altitude: 150,
    city: 'Toulouse',
    image: 'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
  },
];
</script>

<style lang="css" scoped></style>
