<template>
  <form
    class="flex flex-col gap-2"
    role="search"
    @submit.prevent="commitCity"
  >
    <div class="relative">
      <IconSearch class="pointer-events-none absolute top-3 left-3 size-4 text-on-surface/40" />
      <input
        v-model="city"
        type="search"
        enterkeyhint="search"
        autocomplete="off"
        :placeholder="t('filters.city')"
        :aria-label="t('filters.city')"
        :class="[FIELD, 'pl-9']"
      />
    </div>
    <div class="grid grid-cols-2 gap-2">
      <select
        :value="filters.activity"
        :aria-label="t('filters.activity')"
        :class="FIELD"
        @change="emit('update', { activity: value($event) })"
      >
        <option value="">{{ t('filters.all_activities') }}</option>
        <option
          v-for="activity in activities"
          :key="activity.code"
          :value="activity.code"
        >
          {{ activity.label }}
        </option>
        <!-- A shared link may carry a code the API no longer lists: keep it visible. -->
        <option
          v-if="isUnknownActivity"
          :value="filters.activity"
        >
          {{ activityLabel(filters.activity) }}
        </option>
      </select>
      <select
        :value="filters.order"
        :aria-label="t('filters.order')"
        :class="FIELD"
        @change="emit('update', { order: value($event) === 'asc' ? 'asc' : 'desc' })"
      >
        <option value="desc">{{ t('filters.newest_first') }}</option>
        <option value="asc">{{ t('filters.oldest_first') }}</option>
      </select>
    </div>
    <button
      v-if="hasActiveFilters"
      type="button"
      class="self-start text-sm text-on-surface/70 underline"
      @click="emit('reset')"
    >
      {{ t('filters.reset') }}
    </button>
  </form>
</template>

<script lang="ts" setup>
import { computed, onScopeDispose, ref, watch } from 'vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import { useActivities } from '@/composables/useActivities';
import type { CollectionFilters } from '@/composables/useCollectionFilters';
import { useAppI18n } from '@/i18n';

const FIELD =
  'bg-surface-container-low border-on-surface/10 h-10 w-full min-w-0 rounded-2xl border px-3 text-sm';
const CITY_DEBOUNCE_MS = 350;

const props = defineProps<{
  filters: CollectionFilters;
  hasActiveFilters: boolean;
}>();

const emit = defineEmits<{
  (e: 'update', changes: Partial<CollectionFilters>): void;
  (e: 'reset'): void;
}>();

const { t } = useAppI18n();
const { activities, activityLabel } = useActivities();

const isUnknownActivity = computed(
  () =>
    props.filters.activity !== '' &&
    !activities.value.some(({ code }) => code === props.filters.activity),
);

function value(event: Event): string {
  return (event.target as HTMLSelectElement).value;
}

// The city is searched as it is typed, once the typing pauses.
const city = ref(props.filters.city);
let timer: ReturnType<typeof setTimeout> | undefined;

function commitCity() {
  clearTimeout(timer);
  if (city.value.trim() !== props.filters.city) {
    emit('update', { city: city.value.trim() });
  }
}

watch(city, () => {
  clearTimeout(timer);
  timer = setTimeout(commitCity, CITY_DEBOUNCE_MS);
});

// Back/forward navigation or a reset changed the URL: follow it, unless it only caught up
// with what is being typed.
watch(
  () => props.filters.city,
  (fromUrl) => {
    if (fromUrl !== city.value.trim()) {
      city.value = fromUrl;
    }
  },
);

onScopeDispose(() => clearTimeout(timer));
</script>
