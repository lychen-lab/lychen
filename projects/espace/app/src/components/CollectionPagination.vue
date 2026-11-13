<template>
  <nav
    v-if="totalPages > 1"
    class="flex items-center justify-between gap-2"
    :aria-label="t('pagination.label')"
  >
    <Button
      icon-only
      variant="outline"
      size="sm"
      :disabled="page <= 1"
      :label="t('pagination.previous')"
      @click="emit('update:page', page - 1)"
    >
      <template #icon>
        <IconChevronLeft />
      </template>
    </Button>
    <p class="text-sm text-on-surface/60">
      {{ t('pagination.position', { page, total: totalPages }) }}
    </p>
    <Button
      icon-only
      variant="outline"
      size="sm"
      :disabled="page >= totalPages"
      :label="t('pagination.next')"
      @click="emit('update:page', page + 1)"
    >
      <template #icon>
        <IconChevronRight />
      </template>
    </Button>
  </nav>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconChevronLeft from '@lychen/vue-icons/IconChevronLeft.vue';
import IconChevronRight from '@lychen/vue-icons/IconChevronRight.vue';
import { ITEMS_PER_PAGE } from '@/composables/useCollectionFilters';
import { useAppI18n } from '@/i18n';

const props = defineProps<{
  page: number;
  totalItems: number;
}>();

const emit = defineEmits<{
  (e: 'update:page', page: number): void;
}>();

const { t } = useAppI18n();

const totalPages = computed(() => Math.ceil(props.totalItems / ITEMS_PER_PAGE));
</script>
