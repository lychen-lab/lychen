<template>
  <Page
    :title="t('title')"
    :description="t('description')"
  >
    <div class="flex flex-col items-start gap-10 p-4">
      <div
        v-for="persona in personas"
        :key="persona.id"
        class="flex flex-col gap-8 lg:flex-row"
      >
        <CardPersona
          v-bind="persona"
          class="basis-2/3"
        />
        <div class="flex gap-8 lg:flex-col lg:justify-between">
          <CardPersonaOverImage v-bind="persona" />
          <CardPersonaSmall v-bind="persona" />
        </div>
      </div>
    </div>
  </Page>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ref, onMounted, defineAsyncComponent } from 'vue';
import type { Component } from 'vue';

import { messages, TRANSLATION_KEY } from './i18n';

import type { Props as CardPersonaProps } from '@/components/card-persona';
import CardPersonaSmall from '@/components/card-persona/CardPersonaSmall.vue';
import CardPersonaOverImage from '@/components/card-persona/CardPersonaOverImage.vue';

const Page = defineAsyncComponent(() => import('@/components/Page.vue'));

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const CardPersona = defineAsyncComponent<Component<CardPersonaProps>>(
  () => import('@/components/card-persona/CardPersona.vue'),
);

const personas = ref<CardPersonaProps[]>([]);

onMounted(async () => {
  // Dynamically import all .json files from the ./json directory
  const personaModules = import.meta.glob('./json/*.json');
  const loadedPersonas: CardPersonaProps[] = [];

  for (const path in personaModules) {
    const module = await personaModules[path]();
    // Assuming the JSON file's default export is the persona object
    // and it matches the CardPersonaProps interface
    if (module.default) {
      loadedPersonas.push(module.default as CardPersonaProps);
    }
  }

  personas.value = loadedPersonas;
});
</script>

<style lang="css" scoped>
.persona-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(50%, 1fr)); /* Adjust minmax as needed */
  gap: 1rem; /* Adjust gap as needed */
  padding-top: 1rem; /* Add some space below the page header */
}
</style>
