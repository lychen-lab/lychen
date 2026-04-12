<template>
  <div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button
          class="text-on-surface-variant hover:text-on-surface"
          @click="router.back()"
        >
          <IconArrowLeft />
        </button>
        <h1 class="text-on-surface text-2xl font-bold">{{ culture.name }}</h1>
      </div>
      <span class="bg-primary text-on-primary rounded-full px-3 py-1 text-sm font-medium">
        {{ culture.status }}
      </span>
    </div>

    <div class="bg-surface-container rounded-xl p-4">
      <h2 class="text-on-surface border-surface-variant mb-4 border-b pb-2 text-lg font-semibold">
        Details
      </h2>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <span class="text-on-surface-variant mb-1 block">Species</span>
          <span class="font-medium">{{ culture.species }}</span>
        </div>
        <div>
          <span class="text-on-surface-variant mb-1 block">Started</span>
          <span class="font-medium">{{ culture.startDate }}</span>
        </div>
        <div>
          <span class="text-on-surface-variant mb-1 block">Medium</span>
          <span class="font-medium">{{ culture.medium }}</span>
        </div>
        <div>
          <span class="text-on-surface-variant mb-1 block">Location</span>
          <span class="font-medium">{{ culture.location }}</span>
        </div>
      </div>
    </div>

    <div class="flex items-start gap-4 rounded-xl border border-blue-200 bg-blue-50 p-4">
      <div class="mt-1 text-blue-500">
        <IconSun />
      </div>
      <div>
        <h3 class="font-semibold text-blue-900">Weather Expectation (Mocked)</h3>
        <p class="mt-1 text-sm text-blue-800">
          Based on the upcoming forecast (70% humidity, 18°C), this culture is expected to start
          fructification around <span class="font-bold">Next Tuesday</span>.
        </p>
      </div>
    </div>

    <div class="bg-surface-container rounded-xl p-4">
      <div class="border-surface-variant mb-4 flex items-center justify-between border-b pb-2">
        <h2 class="text-on-surface text-lg font-semibold">Lifecycle Timeline</h2>
        <button class="text-primary text-sm font-medium hover:underline">+ Log Event</button>
      </div>

      <div
        class="border-surface-variant relative ml-3 flex flex-col gap-0 space-y-6 border-l-2 pl-4"
      >
        <div
          v-for="(event, index) in timeline"
          :key="index"
          class="relative"
        >
          <div
            class="bg-primary border-surface absolute -left-[23px] mt-1 h-3 w-3 rounded-full border-2 p-1"
          ></div>
          <div class="bg-surface rounded-lg p-3 shadow-sm">
            <div class="flex items-start justify-between">
              <span class="text-on-surface font-semibold">{{ event.type }}</span>
              <span class="text-on-surface-variant text-xs">{{ event.date }}</span>
            </div>
            <p
              v-if="event.note"
              class="text-on-surface-variant mt-2 text-sm"
            >
              {{ event.note }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import IconArrowLeft from '@lychen/vue-icons/IconArrowLeft.vue';
import IconSun from '@lychen/vue-icons/IconSun.vue';

const route = useRoute();
const router = useRouter();
const id = route.params.id;

// Mock data based on ID
const culture = {
  id: id,
  name: 'Shiitake Oak Log #1',
  species: 'Lentinula edodes (Shiitake)',
  status: 'Incubating',
  startDate: '2026-02-15',
  medium: 'Oak Log (1.5m)',
  location: 'Shaded Garden Area B',
};

const timeline = [
  {
    type: 'Purchased Mycelium',
    date: '2026-02-10',
    note: 'Bought from reliable online vendor (100 plugs).',
  },
  {
    type: 'Inoculated',
    date: '2026-02-15',
    note: 'Drilled 50 holes and tapped in plugs. Sealed with wax.',
  },
  { type: 'Watering', date: '2026-03-01', note: 'Soaked for 4 hours to rehydrate after dry week.' },
  { type: 'Watering', date: '2026-03-14', note: 'Light spray.' },
];

// Note: A real app would fetch data using the `id` param.
</script>
