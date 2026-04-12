<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
      <h1 class="text-on-surface text-2xl font-bold">Active Cultures</h1>
      <button class="bg-primary text-on-primary rounded-full px-4 py-2 font-medium">
        + New Culture
      </button>
    </div>

    <div class="flex flex-col gap-3">
      <div
        v-for="culture in fakeCultures"
        :key="culture.id"
        class="bg-surface-container hover:bg-surface-container-high flex cursor-pointer flex-col gap-2 rounded-xl p-4 transition-colors"
        @click="goToCulture(culture.id)"
      >
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-on-surface text-lg font-semibold">{{ culture.name }}</h2>
            <span class="text-on-surface-variant text-sm">{{ culture.species }}</span>
          </div>
          <span
            class="rounded-full px-2 py-1 text-xs font-medium"
            :class="{
              'bg-blue-100 text-blue-800': culture.status === 'Incubating',
              'bg-amber-100 text-amber-800': culture.status === 'Needs Water',
              'bg-green-100 text-green-800': culture.status === 'Ready to Harvest',
            }"
          >
            {{ culture.status }}
          </span>
        </div>

        <div class="mt-2 text-sm">
          <p class="text-on-surface-variant">
            Last Action: <span class="text-on-surface font-medium">{{ culture.lastAction }}</span>
          </p>
          <p
            v-if="culture.nextAction"
            class="text-primary mt-1 font-medium"
          >
            Next: {{ culture.nextAction }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();

const fakeCultures = [
  {
    id: 'c1',
    name: 'Shiitake Oak Log #1',
    species: 'Lentinula edodes',
    status: 'Incubating',
    lastAction: 'Inoculated (2 weeks ago)',
    nextAction: 'First Watering (in 2 months)',
  },
  {
    id: 'c2',
    name: 'Oyster Straw Bag #42',
    species: 'Pleurotus ostreatus',
    status: 'Needs Water',
    lastAction: 'Mycelium fully colonized (2 days ago)',
    nextAction: 'Watering (Today)',
  },
  {
    id: 'c3',
    name: "Lion's Mane Block A",
    species: 'Hericium erinaceus',
    status: 'Ready to Harvest',
    lastAction: 'Pins formed (5 days ago)',
    nextAction: 'Harvest (Today)',
  },
];

function goToCulture(id: string) {
  router.push(`/cultures/${id}`);
}
</script>
