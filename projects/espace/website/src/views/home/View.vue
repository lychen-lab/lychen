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
            Nous recherchons activement des terrains, jardins, caves et autres lieux pour permettre
            d'augmenter l'autonomie alimentaire et créer des liens sociaux.
          </h2>
          <div class="flex flex-col items-start gap-2">
            <Button size="lg">Partagez votre espace</Button>
            <p class="text-xs opacity-80">
              ou envoyez nous un email sur
              <a
                href="mailto:espace@lychen.org"
                class="underline"
                >espace@lychen.org</a
              >
            </p>
          </div>
        </div>
      </div>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8">
    <Title variant="h1">Trouvez un espace de culture</Title>
    <div class="flex w-1/3 flex-row items-center gap-2">
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

      <Input placeholder="Rechercher par ville" />
      <Button
        size="sm"
        label="Rechercher"
        ><template #icon><IconSearch /></template
      ></Button>
    </div>
    <div class="flex w-full flex-row gap-4 overflow-x-scroll pb-2">
      <CardLandProposal
        v-for="item in fakeLandProposals"
        :key="item.uuid"
        v-bind="item"
        display-city
      />
    </div>
    <div class="flex w-full flex-row items-center justify-start gap-4">
      <Badge>10 lieux en France</Badge>
      <Button
        variant="ghost"
        size="sm"
        >Voir tous les espaces <template #icon><IconArrowRight /></template
      ></Button>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8 text-left">
    <h2 class="text-[3rem]/[3rem] lg:text-[5rem]/[5rem]">
      Actuellement,
      <span
        class="font-lexend bg-linear-to-r from-purple-500 via-orange-300 to-sky-500 bg-clip-text font-extrabold text-transparent"
        >10 humains</span
      >
      recherchent un espace de culture.
    </h2>
    <Button>Partagez votre espace</Button>
  </Container>
  <Container
    class="flex flex-col items-center justify-center gap-8 lg:grid lg:grid-cols-3 lg:flex-row lg:gap-8"
  >
    <div class="flex flex-col items-start justify-center gap-4">
      <Title variant="h2">Exemple sur Annecy</Title>
      <Paragraph
        variant="website-default"
        class="opacity-80"
        >Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante feugiat
        finibus. Nullam nec metus vel ante feugiat finibus. Lorem ipsum dolor sit amet, consectetur
        adipiscing elit. Nullam nec metus vel ante feugiat finibus.</Paragraph
      >
    </div>
    <div class="flex items-center justify-center lg:col-span-2">
      <iframe
        class="aspect-video rounded-xl"
        width="100%"
        height="auto"
        src="https://www.youtube.com/embed/t43-zErA2V8?si=3WqOKdcuc0Z992Hx&amp;controls=0"
        title="Jardiner autrement"
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

const { t } = usePrefixedI18n(CONFIG);

const planets = [
  { id: 'earth', img: planetEarth },
  { id: 'mars', img: planetMars },
  { id: 'pluton', img: planetPluton },
];
const fakeLandProposals: LandProposal[] = [
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
  {
    uuid: '3',
    title: 'Fleurs et arbres',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    surface: 75,
    altitude: 0,
    city: 'Lyon',
    image: 'https://images.pexels.com/photos/131772/pexels-photo-131772.jpeg',
  },
  {
    uuid: '4',
    title: 'Terrain de culture',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    surface: 37,
    altitude: 345,
    city: 'Grenoble',
    image: 'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
  },
];
</script>

<style lang="css" scoped></style>
