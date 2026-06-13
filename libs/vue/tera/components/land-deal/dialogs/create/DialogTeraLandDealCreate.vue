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
          <DialogTitle>Partage pour le jardin {{ land?.name }}</DialogTitle>
        </div>
        <div class="flex flex-row gap-2">
          <DialogClose />
        </div>
      </DialogHeader>
      <DialogDescription> Description </DialogDescription>
      <div class="flex flex-row justify-end gap-4">
        <Button label="Envoyer" />
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
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';
import { ref } from 'vue';
import Button from '@lychen/vue-components-core/button/Button.vue';

defineProps<{
  land?: {
    name: string;
    altitude: number;
    surface: number;
    address: {
      city: string;
    };
  };
}>();

const open = ref(false);
</script>
