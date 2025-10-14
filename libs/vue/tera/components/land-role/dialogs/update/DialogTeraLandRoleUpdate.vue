<template>
  <Dialog v-model:open="open">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="max-h-dvh w-full md:max-w-[50%]">
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
        <FormTeraLandRoleUpdate :land-role="landRole" />
        <Separator class="bg-surface-container-highest" />
        <div class="flex flex-row justify-end gap-2">
          <DialogTeraLandRoleDelete :land-role="landRole">
            <Button
              :label="tLandRole('action.delete.label')"
              variant="negative"
            />
          </DialogTeraLandRoleDelete>
        </div>
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
  DialogDescription,
} from '@lychen/vue-components-core/dialog';
import FormTeraLandRoleUpdate from '@lychen/vue-tera/components/land-role/forms/FormTeraLandRoleUpdate.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';
import { useEventBus } from '@vueuse/core';
import {
  landRoleDeleteSucceededEvent,
  landRolePatchSucceededEvent,
} from '@lychen/vue-tera/events/LandRoleEvents';
import { ref } from 'vue';
import {
  messages as landRoleMessages,
  TRANSLATION_KEY as LAND_ROLE_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-role';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { Separator } from '@lychen/vue-components-core/separator';
import DialogTeraLandRoleDelete from '../delete/DialogTeraLandRoleDelete.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { t: tLandRole } = useI18nExtended({
  messages: landRoleMessages,
  rootKey: LAND_ROLE_TRANSLATION_KEY,
  prefixed: true,
});

const { landRole } = defineProps<{ landRole: components['schemas']['LandRole.jsonld'] }>();

const open = ref(false);

const { on } = useEventBus(landRolePatchSucceededEvent);
const { on: onDelete } = useEventBus(landRoleDeleteSucceededEvent);

on(() => {
  open.value = false;
});
onDelete(() => {
  open.value = false;
});
</script>
