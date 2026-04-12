<template>
  <Container class="flex flex-col items-center justify-center gap-4 py-20">
    <div
      class="border-surface-container relative flex flex-col items-center gap-8 rounded-2xl border-4 p-4 lg:flex-row"
    >
      <div class="absolute top-0 left-0 h-full w-full overflow-hidden rounded-2xl">
        <img
          src="https://images.pexels.com/photos/1573885/pexels-photo-1573885.jpeg"
          alt=""
          class="h-full w-full object-cover"
        />
      </div>
      <div class="z-10 flex flex-col items-center gap-4 lg:items-end">
        <div
          class="bg-surface-container/70 z-10 flex w-full flex-col items-start gap-4 rounded-2xl px-8 py-10 backdrop-blur-md lg:w-1/3"
        >
          <IconHeartHandshake class="size-18!" />
          <h2 class="text-on-white text-xl font-bold">
            {{ t('hero.description') }}
          </h2>
          <div class="flex flex-col items-start gap-2">
            <a
              :href="LINK.LandProposalForm"
              target="_blank"
            >
              <Button size="lg"
                >{{ t('hero.cta_share') }} <template #icon><IconArrowRight /></template
              ></Button>
            </a>
            <p class="text-xs opacity-80">
              {{ t('hero.email_prefix') }}
              <a
                href="mailto:contact@lychen.org"
                class="underline"
                >contact@lychen.org</a
              >
            </p>
          </div>
        </div>
      </div>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8">
    <Title variant="h1">{{ t('search.title') }}</Title>
    <div class="flex flex-row items-center gap-2">
      <Paragraph>{{ t('search.subtitle') }}</Paragraph>
      <a
        :href="LINK.LandRequestForm"
        target="_blank"
      >
        <Button
          >{{ t('search.cta_form') }} <template #icon><IconArrowRight /></template
        ></Button>
      </a>
    </div>
    <div class="flex w-1/3 flex-row items-center gap-2 opacity-20">
      <Popover>
        <PopoverTrigger as-child>
          <Button
            icon-only
            variant="ghost"
            size="sm"
            ><template #icon> <img :src="planetEarth" /></template
          ></Button>
        </PopoverTrigger>
        <PopoverContent class="flex w-fit flex-row gap-2">
          <img
            v-for="planet in planets"
            :key="planet.id"
            :src="planet.img"
            class="size-10 cursor-pointer"
          />
        </PopoverContent>
      </Popover>

      <Input :placeholder="t('search.placeholder_city')" />
      <Button
        size="sm"
        :label="t('search.btn_search')"
        ><template #icon><IconSearch /></template
      ></Button>
    </div>
    <div class="flex w-full flex-row gap-4 overflow-x-scroll pb-2 opacity-20">
      <CardLandProposal
        v-for="item in fakeLandProposals"
        :key="item.uuid"
        v-bind="item"
        display-city
      />
    </div>
    <div class="flex w-full flex-row items-center justify-start gap-4 opacity-20">
      <Badge>{{ t('search.stats_count') }}</Badge>
      <Button
        variant="ghost"
        size="sm"
        >{{ t('search.btn_all') }} <template #icon><IconArrowRight /></template
      ></Button>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8 text-left">
    <h2 class="text-[3rem]/[3rem] lg:text-[5rem]/[5rem]">
      {{ t('cta_bottom.line_1') }}
      <span
        class="font-lexend bg-linear-to-r from-purple-500 via-orange-300 to-sky-500 bg-clip-text font-extrabold text-transparent"
        >{{ t('cta_bottom.line_2') }}</span
      >
      {{ t('cta_bottom.line_3') }}
    </h2>
    <a
      :href="LINK.LandProposalForm"
      target="_blank"
    >
      <Button size="lg"
        >{{ t('cta_bottom.cta_share') }} <template #icon><IconArrowRight /></template
      ></Button>
    </a>
  </Container>
  <Container
    class="flex flex-col items-center justify-center gap-8 lg:grid lg:grid-cols-3 lg:flex-row lg:gap-8"
  >
    <div class="flex flex-col items-start justify-center gap-4">
      <Title variant="h2">{{ t('example.title') }}</Title>
      <Paragraph
        variant="website-default"
        class="opacity-80"
        >{{ t('example.paragraph') }}</Paragraph
      >
    </div>
    <div class="flex items-center justify-center lg:col-span-2">
      <iframe
        class="aspect-video rounded-xl"
        width="100%"
        height="auto"
        src="https://www.youtube.com/embed/t43-zErA2V8?si=3WqOKdcuc0Z992Hx&amp;controls=0"
        :title="t('example.video_title')"
        frameborder="0"
        allow="
          accelerometer;
          autoplay;
          clipboard-write;
          encrypted-media;
          gyroscope;
          picture-in-picture;
          web-share;
        "
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen
      ></iframe>
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { type ComputedRef, computed } from 'vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  Card as CardLandProposal,
  type LandProposal,
} from '@lychen/vue-components-business/land-proposal/card';
import { Container } from '@lychen/vue-components-website/container';
import { Title } from '@lychen/vue-components-website/title';
import { Paragraph } from '@lychen/vue-components-website/paragraph';
import { Input } from '@lychen/vue-components-core/input';
import { Button } from '@lychen/vue-components-core/button';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import { CONFIG } from './i18n';
import { Badge } from '@lychen/vue-components-core/badge';
import planetEarth from './assets/planet-earth.png';
import planetPluton from './assets/planet-pluton.png';
import planetMars from './assets/planet-mars.png';
import { Popover, PopoverContent, PopoverTrigger } from '@lychen/vue-components-core/popover';
import { LINK } from '@lychen/typescript-espace/constants/App';

const { t } = usePrefixedI18n(CONFIG);

const planets = [
  { id: 'earth', img: planetEarth },
  { id: 'mars', img: planetMars },
  { id: 'pluton', img: planetPluton },
];
const fakeLandProposals: ComputedRef<LandProposal[]> = computed(() => [
  {
    uuid: '1',
    title: t('search.result_1_title'),
    description: t('search.result_description'),
    surface: 130,
    altitude: 678,
    city: 'Lille',
    image: 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
  },
  {
    uuid: '2',
    title: t('search.result_2_title'),
    description: t('search.result_description'),
    surface: 40,
    altitude: 150,
    city: 'Toulouse',
    image: 'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
  },
  {
    uuid: '3',
    title: t('search.result_3_title'),
    description: t('search.result_description'),
    surface: 75,
    altitude: 0,
    city: 'Lyon',
    image: 'https://images.pexels.com/photos/131772/pexels-photo-131772.jpeg',
  },
  {
    uuid: '4',
    title: t('search.result_1_title'),
    description: t('search.result_description'),
    surface: 37,
    altitude: 345,
    city: 'Grenoble',
    image: 'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
  },
]);
</script>

<style lang="css" scoped></style>
