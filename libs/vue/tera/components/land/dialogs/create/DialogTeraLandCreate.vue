<template>
  <Dialog>
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>

    <DialogContent class="max-h-dvh w-full gap-8 overflow-y-auto md:max-w-[30%]">
      <DialogHeader class="flex flex-row items-start justify-between gap-10">
        <div class="flex flex-col gap-2 md:w-4/5">
          <DialogTitle>{{ t('title') }}</DialogTitle>
          <DialogDescription>
            {{ t('description') }}
          </DialogDescription>
          <p class="text-sm italic">{{ t('sub_description') }}</p>
        </div>
        <DialogClose />
      </DialogHeader>
      <FormTeraLandCreate />
    </DialogContent>
  </Dialog>
</template>

<script lang="ts" setup>
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@lychen/vue-components-core/dialog';
import DialogDescription from '@lychen/vue-components-core/dialog/DialogDescription.vue';
import FormTeraLandCreate from '@lychen/vue-tera/components/land/forms/FormTeraLandCreate.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
