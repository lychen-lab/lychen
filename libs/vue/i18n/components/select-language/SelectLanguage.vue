<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        icon-only
        size="sm"
        variant="ghost"
      >
        <template #icon>
          <IconLanguage />
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-auto"
      side="top"
      :side-offset="20"
    >
      <div
        v-for="(localeItem, index) in availableLocales"
        :key="index"
        class="hover:bg-surface-container-high cursor-pointer rounded-xl p-2 px-4"
        @click="switchLanguage(localeItem.code)"
      >
        {{ localeItem.name }}
      </div>
    </PopoverContent>
  </Popover>
</template>

<script lang="ts" setup>
import { Popover, PopoverContent, PopoverTrigger } from '@lychen/vue-components-core/popover';
import IconLanguage from '@lychen/vue-icons/IconLanguage.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useTrans } from '../../composables/useTrans';

const { switchLanguage } = useTrans();

const availableLocales = [
  { code: 'en-US', name: 'English' },
  { code: 'fr-FR', name: 'Français' },
];
</script>
