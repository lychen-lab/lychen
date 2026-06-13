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
          <DialogTitle>{{ title }}</DialogTitle>
        </div>
        <div class="flex flex-row gap-2">
          <Button
            label="Partager"
            variant="ghost"
            size="sm"
          >
            <template #icon><IconShare2 /></template
          ></Button>
          <DialogClose />
        </div>
      </DialogHeader>
      <DialogDescription>
        {{ description }}
      </DialogDescription>
      <div class="flex flex-col gap-2">
        Conditions de partage
        <TeraLandProposalSharingConditions
          :sharing-conditions="sharingConditions"
          icons-border-class="border-surface-container-high/90 border-3"
          :display="DISPLAY.Vertical"
          display-label
        />
      </div>
      <div class="flex flex-row justify-end gap-4">
        <Button label="Candidater" />
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
import IconShare2 from '@lychen/vue-icons/IconShare2.vue';
import TeraLandProposalSharingConditions from '../../../common/sharing-conditions-icons/TeraLandProposalSharingConditions.vue';
import { DISPLAY } from '../../../common/sharing-conditions-icons';
import type { LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';

defineProps<{
  title?: string;
  description?: unknown[] | null;
  preferredInteractionMode?: string;
  sharingConditions?: LandSharingCondition[];
  expirationDate?: string;
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
