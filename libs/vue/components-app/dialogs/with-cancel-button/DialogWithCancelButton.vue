<template>
  <Dialog>
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="max-h-dvh w-full gap-8 overflow-y-auto md:max-w-[30%]">
      <DialogHeader>
        <slot name="header">
          <DialogTitle>{{ title }}</DialogTitle>
          <DialogDescription> {{ description }} </DialogDescription>
        </slot>
      </DialogHeader>
      <slot name="content" />
      <DialogFooter>
        <slot name="footer">
          <DialogClose
            v-if="hasCancelButton"
            class="flex flex-row justify-end gap-6"
          >
            <Button variant="ghost">{{ t('cancel.label') }}</Button>

            <slot name="action" />
          </DialogClose>
        </slot>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import {
  DialogClose,
  DialogContent,
  DialogHeader,
  DialogFooter,
  DialogTitle,
  DialogTrigger,
  Dialog,
  DialogDescription,
} from '@lychen/vue-components-core/dialog';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';

const {
  title,
  description,
  hasCancelButton = true,
} = defineProps<{
  title: string;
  description: string;
  hasCancelButton?: boolean;
}>();

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});
</script>
