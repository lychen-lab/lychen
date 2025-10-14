<template>
  <Dialog v-model:open="open">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent
      class="bg-surface-container-high/90 text-on-surface-container max-h-dvh w-full md:max-w-[50%]"
    >
      <DialogHeader class="flex flex-row items-center justify-between gap-10">
        <div class="flex flex-col gap-2">
          <DialogTitle>Gérer vos annonces</DialogTitle>
        </div>
        <div class="flex flex-row gap-2">
          <DialogClose />
        </div>
      </DialogHeader>
      <DialogDescription>
        Une seule annonce publiée par espace de culture. Publier celle-ci archivera la précédente.
      </DialogDescription>
      <div class="flex flex-row justify-end gap-4">
        <Button label="Publier" />
      </div>
    </DialogContent>
  </Dialog>
</template>

<script lang="ts" setup>
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@lychen/vue-components-core/dialog';
import DialogDescription from '@lychen/vue-components-core/dialog/DialogDescription.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';
import { ref, computed } from 'vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const open = ref(false);

const land = ref<{ ulid: string }>();
const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);
const { api } = useTeraApi();

const { data: landProposals } = useQuery({
  queryKey: ['landProposalsCollection', land],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.ulid');
    }
    const response = await api.GET('/api/land_proposals', {
      params: { query: { land: landUlid.value } },
    });
    return response.data;
  },
  enabled,
});
</script>
