<template>
  <div class="flex flex-col gap-0">
    <!-- Hero gallery full-bleed, square format -->
    <div class="relative -mx-4 -mt-4 aspect-square w-screen">
      <div
        ref="galleryRef"
        class="gallery-scroll flex h-full snap-x snap-mandatory overflow-x-auto"
        @scroll="onGalleryScroll"
      >
        <div
          v-for="(img, i) in terrain.gallery"
          :key="i"
          class="relative h-full w-full shrink-0 snap-center"
        >
          <img
            :src="img"
            :alt="terrain.title"
            class="h-full w-full object-cover"
            :loading="i === 0 ? 'eager' : 'lazy'"
            decoding="async"
          />
        </div>
      </div>

      <!-- Gradient overlay top -->
      <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-black/50 via-transparent to-black/30"
      />

      <!-- Top controls -->
      <div class="absolute top-0 right-0 left-0 flex items-center justify-between p-4 pt-6">
        <button
          class="flex size-10 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm transition hover:bg-black/50"
          @click="$router.back()"
        >
          <IconArrowLeft class="size-5" />
        </button>
        <div class="flex gap-2">
          <button
            class="flex size-10 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm transition hover:bg-black/50"
          >
            <IconShare2 class="size-5" />
          </button>
          <button
            class="flex size-10 items-center justify-center rounded-full bg-black/30 backdrop-blur-sm transition hover:bg-black/50"
            :class="isFavorite ? 'text-red-400' : 'text-white'"
            @click="isFavorite = !isFavorite"
          >
            <IconHeartFilled
              v-if="isFavorite"
              class="size-5"
            />
            <IconHeart
              v-else
              class="size-5"
            />
          </button>
        </div>
      </div>

      <!-- Image counter dots -->
      <div class="absolute right-0 bottom-3 left-0 flex justify-center gap-1.5">
        <div
          v-for="(_, i) in terrain.gallery"
          :key="i"
          class="rounded-full transition-all duration-300"
          :class="i === activeIndex ? 'size-2 bg-white' : 'size-1.5 bg-white/50'"
        />
      </div>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-6 pt-5 pb-36">
      <!-- Title block -->
      <div class="flex flex-col gap-2">
        <div
          v-if="terrain.isVerified"
          class="flex items-center gap-1.5"
        >
          <IconShieldCheck class="size-4 text-lime-600" />
          <span class="text-xs font-semibold tracking-widest text-lime-600 uppercase"
            >Terrain vérifié</span
          >
        </div>
        <h1 class="text-2xl leading-tight font-bold">{{ terrain.title }}</h1>
        <div class="text-on-surface/60 flex items-center gap-1.5 text-sm">
          <IconMapPin class="size-4 shrink-0" />
          <span>{{ terrain.city }}{{ terrain.district ? ` — ${terrain.district}` : '' }}</span>
        </div>
      </div>

      <!-- Feature chips -->
      <div class="flex flex-row gap-2 overflow-x-auto pb-1">
        <div
          class="bg-surface-container flex shrink-0 flex-col items-center gap-1.5 rounded-2xl px-4 py-3"
        >
          <IconBrightness class="size-5 text-amber-500" />
          <span class="text-xs font-medium">{{ terrain.sunExposure }}</span>
        </div>
        <div
          class="bg-surface-container flex shrink-0 flex-col items-center gap-1.5 rounded-2xl px-4 py-3"
        >
          <IconSprout class="size-5 text-lime-600" />
          <span class="text-xs font-medium">{{ terrain.waterAccess }}</span>
        </div>
        <div
          class="bg-surface-container flex shrink-0 flex-col items-center gap-1.5 rounded-2xl px-4 py-3"
        >
          <IconPickaxe class="size-5 text-stone-500" />
          <span class="text-xs font-medium">{{
            terrain.toolsAvailable === true
              ? 'Outils fournis'
              : terrain.toolsAvailable === false
                ? 'Sans outils'
                : 'Outils à discuter'
          }}</span>
        </div>
        <div
          class="bg-surface-container flex shrink-0 flex-col items-center gap-1.5 rounded-2xl px-4 py-3"
        >
          <IconSquareDashed class="size-5 text-lime-700" />
          <span class="text-xs font-medium">{{ terrain.surface }} m²</span>
        </div>
        <div
          class="bg-surface-container flex shrink-0 flex-col items-center gap-1.5 rounded-2xl px-4 py-3"
        >
          <IconMountain class="size-5 text-slate-500" />
          <span class="text-xs font-medium">{{ terrain.altitude }} m alt.</span>
        </div>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Description -->
      <div class="flex flex-col gap-2">
        <h2 class="font-semibold">À propos du terrain</h2>
        <p class="text-on-surface/70 text-sm leading-relaxed">{{ terrain.description }}</p>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Host card -->
      <div class="bg-surface-container flex items-center gap-3 rounded-2xl p-4">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-lime-100">
          <IconUser class="size-6 text-lime-700" />
        </div>
        <div class="flex flex-col gap-0.5">
          <span class="text-sm font-semibold">{{ terrain.host.name }}</span>
          <span class="text-on-surface/60 text-xs"
            >Membre depuis {{ terrain.host.memberSince }}</span
          >
          <div class="flex items-center gap-1 text-xs">
            <IconStar class="size-3.5 fill-amber-400 text-amber-400" />
            <span class="font-medium">{{ terrain.host.rating }}</span>
          </div>
        </div>
        <button class="ml-auto text-xs font-semibold tracking-wide text-lime-700 uppercase">
          Profil →
        </button>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Details grid -->
      <div class="flex flex-col gap-3">
        <h2 class="font-semibold">Détails du terrain</h2>
        <div class="grid grid-cols-2 gap-2">
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Type de lieu</span>
            <span class="text-sm font-medium">{{ terrain.type }}</span>
          </div>
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">État actuel</span>
            <span class="text-sm font-medium">{{ terrain.state }}</span>
          </div>
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Accès au terrain</span>
            <span class="text-sm font-medium">{{ terrain.accessType }}</span>
          </div>
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Fréquence</span>
            <span class="text-sm font-medium">{{ terrain.frequency }}</span>
          </div>
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Stockage matériel</span>
            <span class="text-sm font-medium">{{
              terrain.storageAvailable === true
                ? 'Possible sur place'
                : terrain.storageAvailable === false
                  ? 'Non disponible'
                  : 'À discuter'
            }}</span>
          </div>
          <div class="bg-surface-container flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Culture naturelle</span>
            <span class="text-sm font-medium">{{ terrain.naturalCulture }}</span>
          </div>
          <div class="bg-surface-container col-span-2 flex flex-col gap-1 rounded-xl p-3">
            <span class="text-on-surface/50 text-xs">Partage des récoltes</span>
            <span class="text-sm font-medium">{{ terrain.harvestShare }}</span>
          </div>
        </div>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Goals -->
      <div class="flex flex-col gap-3">
        <h2 class="font-semibold">Ce que recherche le propriétaire</h2>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="goal in terrain.goals"
            :key="goal"
            class="rounded-full bg-lime-50 px-3 py-1 text-xs font-medium text-lime-800"
          >
            {{ goal }}
          </span>
        </div>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Rules -->
      <div class="flex flex-col gap-3">
        <h2 class="font-semibold">Règles du terrain</h2>
        <ul class="flex flex-col gap-2">
          <li
            v-for="rule in terrain.rules"
            :key="rule"
            class="flex items-start gap-2 text-sm"
          >
            <IconCircleCheck class="mt-0.5 size-4 shrink-0 text-lime-600" />
            <span class="text-on-surface/80">{{ rule }}</span>
          </li>
        </ul>
      </div>

      <div class="border-on-surface/10 border-t" />

      <!-- Map placeholder -->
      <div class="flex flex-col gap-3">
        <h2 class="font-semibold">Localisation</h2>
        <div
          class="border-on-surface/10 bg-surface-container flex h-44 items-center justify-center rounded-2xl border"
        >
          <div class="flex flex-col items-center gap-2">
            <div class="bg-on-surface/10 flex size-10 items-center justify-center rounded-full">
              <IconMapPin class="text-on-surface/40 size-5" />
            </div>
            <p class="text-sm font-medium">{{ terrain.city }}</p>
            <p
              v-if="terrain.district"
              class="text-on-surface/50 text-xs"
            >
              {{ terrain.district }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Fixed bottom action bar (above nav) -->
  <div
    class="border-on-surface/10 bg-surface/95 fixed right-0 bottom-17.5 left-0 flex items-center gap-3 border-t px-4 py-3 backdrop-blur-sm"
  >
    <button
      class="flex-1 rounded-full bg-lime-700 py-3.5 text-sm font-semibold text-white transition hover:bg-lime-800"
    >
      Demander l'accès
    </button>
    <button
      class="border-on-surface/10 bg-surface-container flex size-12 shrink-0 items-center justify-center rounded-full border transition"
    >
      <IconMessagesSquare class="text-on-surface size-5" />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import IconArrowLeft from '@lychen/vue-icons/IconArrowLeft.vue';
import IconShare2 from '@lychen/vue-icons/IconShare2.vue';
import IconHeart from '@lychen/vue-icons/IconHeart.vue';
import IconHeartFilled from '@lychen/vue-icons/IconHeartFilled.vue';
import IconShieldCheck from '@lychen/vue-icons/IconShieldCheck.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import IconBrightness from '@lychen/vue-icons/IconBrightness.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconPickaxe from '@lychen/vue-icons/IconPickaxe.vue';
import IconSquareDashed from '@lychen/vue-icons/IconSquareDashed.vue';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import IconUser from '@lychen/vue-icons/IconUser.vue';
import IconStar from '@lychen/vue-icons/IconStar.vue';
import IconCircleCheck from '@lychen/vue-icons/IconCircleCheck.vue';
import IconMessagesSquare from '@lychen/vue-icons/IconMessagesSquare.vue';

useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const route = useRoute();
const isFavorite = ref(false);
const galleryRef = ref<HTMLElement | null>(null);
const activeIndex = ref(0);

function onGalleryScroll() {
  if (!galleryRef.value) return;
  const { scrollLeft, clientWidth } = galleryRef.value;
  activeIndex.value = Math.round(scrollLeft / clientWidth);
}

const allTerrains = [
  {
    uuid: '1',
    title: 'Terrain de culture',
    description:
      "Grand terrain ensoleillé idéal pour la culture maraîchère. Sol argileux riche en nutriments, eau disponible sur place. Le terrain est clôturé et dispose d'un accès indépendant via un portail. Parfait pour démarrer un potager productif dès le printemps.",
    surface: 130,
    altitude: 678,
    city: 'Lille',
    district: 'Wazemmes',
    image: 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
    gallery: [
      'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
      'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
      'https://images.pexels.com/photos/213399/pexels-photo-213399.jpeg',
    ],
    isVerified: true,
    type: 'Terrain nu',
    sunExposure: 'Plein soleil',
    state: 'Prêt à cultiver',
    waterAccess: 'Eau à proximité',
    toolsAvailable: true,
    storageAvailable: 'discuss' as const,
    accessType: 'Accès indépendant',
    frequency: 'Libre',
    harvestShare: '50/50 avec le propriétaire',
    naturalCulture: 'Indispensable',
    goals: ['Production de légumes', 'Biodiversité', 'Entretien du terrain'],
    host: { name: 'Sophie M.', memberSince: '2022', rating: 4.7 },
    rules: [
      'Culture 100% naturelle obligatoire — sans pesticides ni engrais chimiques.',
      'Accès libre 7j/7, portail avec code fourni à la signature.',
      'Récolte partagée à 50/50 chaque fin de saison.',
      'Laisser le terrain propre et rangé après chaque visite.',
    ],
  },
  {
    uuid: '2',
    title: 'Cave à champignons',
    description:
      'Cave naturelle fraîche et humide, parfaite pour la culture de champignons en toute saison. Température stable entre 10 et 15°C. Idéale pour pleurotes, shiitake et champignons de Paris.',
    surface: 40,
    altitude: 150,
    city: 'Toulouse',
    district: 'Saint-Cyprien',
    image: 'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
    gallery: [
      'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
      'https://images.pexels.com/photos/1389460/pexels-photo-1389460.jpeg',
    ],
    isVerified: false,
    type: 'Garage',
    sunExposure: 'Ombre',
    state: 'Prêt à cultiver',
    waterAccess: "Point d'eau sur place",
    toolsAvailable: false,
    storageAvailable: true,
    accessType: 'En ma présence',
    frequency: '1-2 fois par semaine',
    harvestShare: 'À discuter selon les cultures',
    naturalCulture: 'Souhaitée',
    goals: ['Production de légumes', 'Rencontre et échange'],
    host: { name: 'Pierre L.', memberSince: '2023', rating: 4.5 },
    rules: [
      'Accès uniquement en présence du propriétaire — contacter la veille.',
      'Pas de produits chimiques dans la cave.',
      'Matériel de culture à apporter soi-même.',
    ],
  },
  {
    uuid: '3',
    title: 'Jardin partagé',
    description:
      "Espace vert urbain divisé en plusieurs parcelles. Accès facile, composteur et outils disponibles sur place. Idéal pour débuter le jardinage en ville avec l'aide d'une communauté bienveillante.",
    surface: 220,
    altitude: 45,
    city: 'Lyon',
    district: 'Croix-Rousse',
    image: 'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
    gallery: [
      'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
      'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
      'https://images.pexels.com/photos/4750270/pexels-photo-4750270.jpeg',
    ],
    isVerified: true,
    type: 'Jardin privé',
    sunExposure: 'Mi-ombre',
    state: 'Potager déjà en place',
    waterAccess: 'Récupérateur pluie',
    toolsAvailable: true,
    storageAvailable: true,
    accessType: 'Accès indépendant',
    frequency: 'Libre',
    harvestShare: 'Je ne souhaite pas de récolte',
    naturalCulture: 'Indispensable',
    goals: ['Biodiversité', 'Rencontre et échange', 'Entretien du terrain'],
    host: { name: 'Marie D.', memberSince: '2021', rating: 4.9 },
    rules: [
      'Culture biologique uniquement — label nature urbaine respecté.',
      'Composteur collectif obligatoire pour les déchets verts.',
      'Arroser les parcelles voisines lors des canicules.',
      "Pas d'animaux dans l'espace potager.",
    ],
  },
  {
    uuid: '4',
    title: 'Serre horticole',
    description:
      "Serre en verre chauffée, idéale pour les cultures tropicales ou les semis précoces de printemps. Équipée d'un système d'arrosage automatique et de tablettes modulables.",
    surface: 75,
    altitude: 320,
    city: 'Bordeaux',
    district: 'Chartrons',
    image: 'https://images.pexels.com/photos/1389460/pexels-photo-1389460.jpeg',
    gallery: [
      'https://images.pexels.com/photos/1389460/pexels-photo-1389460.jpeg',
      'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
    ],
    isVerified: true,
    type: 'Jardin privé',
    sunExposure: 'Plein soleil',
    state: 'Prêt à cultiver',
    waterAccess: 'Arrosage automatique',
    toolsAvailable: true,
    storageAvailable: true,
    accessType: 'Accès par la maison',
    frequency: 'Uniquement en semaine',
    harvestShare: '50/50 avec le propriétaire',
    naturalCulture: 'Souhaitée',
    goals: ['Production de légumes', 'Biodiversité'],
    host: { name: 'Julien R.', memberSince: '2022', rating: 4.6 },
    rules: [
      'Accès du lundi au vendredi, 8h-18h uniquement.',
      'Fermer et verrouiller la serre après chaque visite.',
      "Signaler tout problème de chauffage ou d'arrosage immédiatement.",
    ],
  },
  {
    uuid: '5',
    title: 'Verger familial',
    description:
      'Verger de 30 arbres fruitiers : pommiers, poiriers, pruniers. Récolte partagée entre propriétaire et cultivateur. Sol enherbé entretenu, filets anti-grêle disponibles.',
    surface: 500,
    altitude: 210,
    city: 'Strasbourg',
    district: 'Robertsau',
    image: 'https://images.pexels.com/photos/213399/pexels-photo-213399.jpeg',
    gallery: [
      'https://images.pexels.com/photos/213399/pexels-photo-213399.jpeg',
      'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
      'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
    ],
    isVerified: false,
    type: 'Verger',
    sunExposure: 'Plein soleil',
    state: 'Déjà cultivé',
    waterAccess: "Point d'eau à proximité",
    toolsAvailable: 'discuss' as const,
    storageAvailable: true,
    accessType: 'Accès indépendant',
    frequency: 'Libre',
    harvestShare: '50/50 avec le propriétaire',
    naturalCulture: 'Indispensable',
    goals: ['Production de légumes', 'Entretien du terrain', 'Biodiversité'],
    host: { name: 'Élise K.', memberSince: '2020', rating: 4.8 },
    rules: [
      'Taille des arbres à coordonner avec le propriétaire chaque automne.',
      'Utilisation de traitements naturels uniquement (bouillie bordelaise autorisée).',
      'Récolte 50/50 — accord signé en début de saison.',
    ],
  },
  {
    uuid: '6',
    title: 'Balcon potager',
    description:
      'Grand balcon exposé plein sud, idéal pour les herbes aromatiques, tomates cerises et légumes en pots. Vue dégagée, très lumineux du matin au soir.',
    surface: 12,
    altitude: 28,
    city: 'Paris',
    district: '10ème arrondissement',
    image: 'https://images.pexels.com/photos/4750270/pexels-photo-4750270.jpeg',
    gallery: [
      'https://images.pexels.com/photos/4750270/pexels-photo-4750270.jpeg',
      'https://images.pexels.com/photos/1389460/pexels-photo-1389460.jpeg',
    ],
    isVerified: false,
    type: 'Balcon/Terrasse',
    sunExposure: 'Plein soleil',
    state: 'Pelouse à retourner',
    waterAccess: "Point d'eau sur place",
    toolsAvailable: false,
    storageAvailable: false,
    accessType: 'En ma présence',
    frequency: '1-2 fois par semaine',
    harvestShare: 'Je ne souhaite pas de récolte',
    naturalCulture: 'Souhaitée',
    goals: ['Entretien du terrain', 'Rencontre et échange'],
    host: { name: 'Camille T.', memberSince: '2024', rating: 4.3 },
    rules: [
      'Accès uniquement en présence de la propriétaire.',
      'Utiliser uniquement des pots et bacs fournis.',
      'Pas de plantes envahissantes ni de grandes cultures.',
    ],
  },
];

const terrain = computed(() => {
  const found = allTerrains.find((item) => item.uuid === route.params.uuid);
  if (found) {
    return found;
  }
  const fallback = allTerrains[0];
  if (!fallback) {
    throw new Error('allTerrains must contain at least one terrain');
  }
  return fallback;
});
</script>

<style lang="css" scoped>
.gallery-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.gallery-scroll::-webkit-scrollbar {
  display: none;
}
</style>
