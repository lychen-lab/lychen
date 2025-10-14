<template>
  <Dialog v-model:open="open">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent
      class="bg-surface-container-high/90 text-on-surface-container max-h-dvh w-full md:max-w-[50%]"
    >
      <div class="flex flex-col gap-4 overflow-y-auto">
        <DialogHeader class="flex flex-row items-start justify-between gap-10">
          <div class="flex flex-col gap-2 md:w-4/5">
            <DialogTitle>{{ t('title') }}</DialogTitle>
            <DialogDescription>
              {{ t('description') }}
            </DialogDescription>
          </div>
          <DialogClose />
        </DialogHeader>
        <FormTeraLandRoleCreate :land />
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
import FormTeraLandRoleCreate from '@lychen/vue-tera/components/land-role/forms/FormTeraLandRoleCreate.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';
import { useEventBus } from '@vueuse/core';
import { landRolePostSucceededEvent } from '@lychen/vue-tera/events/LandRoleEvents';
import { ref } from 'vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { land } = defineProps<{ land: components['schemas']['Land.jsonld'] }>();

const open = ref(false);

const { on } = useEventBus(landRolePostSucceededEvent);
on(() => {
  open.value = false;
});
</script>
