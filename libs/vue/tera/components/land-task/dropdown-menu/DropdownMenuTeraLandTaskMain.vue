<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child><slot /></DropdownMenuTrigger>
    <DropdownMenuContent class="w-56">
      <DropdownMenuGroup>
        <DropdownMenuItem
          v-if="isSupported && landTask.ulid"
          @click="copy(landTask.ulid)"
        >
          <IconHash />
          <span>Copier l'ID</span>
        </DropdownMenuItem>
        <DropdownMenuItem
          v-if="isSupported && landTask.ulid"
          @click="copy(landTaskURL)"
        >
          <IconClipboardCopy />
          <span>Copier le lien</span>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <DropdownMenuItem>
          <span>Dupliquer</span>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem
        class="text-negative focus:bg-negative-container focus:text-on-negative-container"
        @click="openDeleteDialog = true"
      >
        {{ tLandTask('action.delete.label') }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
  <DialogTeraLandTaskDelete
    v-model:open="openDeleteDialog"
    :land-task="landTask"
  />
</template>

<script setup lang="ts">
import type { LandTaskJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { useClipboard } from '@vueuse/core';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@lychen/vue-components-core/dropdown-menu';
import {
  messages as landTaskMessages,
  TRANSLATION_KEY as LAND_TASK_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-task';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { defineAsyncComponent, computed, ref } from 'vue';
import IconHash from '@lychen/vue-icons/IconHash.vue';
import IconClipboardCopy from '@lychen/vue-icons/IconClipboardCopy.vue';
import { useRouter } from 'vue-router';

const DialogTeraLandTaskDelete = defineAsyncComponent(
  () => import('../dialogs/delete/DialogTeraLandTaskDelete.vue'),
);

const router = useRouter();

const landTaskURL = computed(() => {
  return window.location.origin + router.resolve({ query: { taskId: landTask.ulid } }).href;
});

const { copy, isSupported } = useClipboard();

const { landTask } = defineProps<{ landTask: LandTaskJsonld }>();

const { t: tLandTask } = useI18nExtended({
  messages: landTaskMessages,
  rootKey: LAND_TASK_TRANSLATION_KEY,
  prefixed: true,
});

const openDeleteDialog = ref(false);
</script>
