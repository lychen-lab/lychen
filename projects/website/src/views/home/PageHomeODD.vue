<template>
  <Container class="flex flex-col items-center gap-4">
    <div class="mt-10 flex w-full flex-col-reverse gap-8 md:grid md:grid-cols-[30%_1fr]">
      <div class="flex flex-col justify-between gap-10">
        <Title variant="h2">{{ t('goals.title') }}</Title>
        <GoalSubSection
          v-for="goal in goals"
          :key="goal.index"
          :title="goal.title"
          :description="goal.description"
          :link="{
            title: t(`goals.link_title.${goal.index}`),
            href: goal.link,
          }"
          :expanded="goal.index === selectedGoal.index"
          @click="selectedGoal = goal"
        />
      </div>
      <div class="flex flex-col items-center justify-center rounded-2xl md:p-14">
        <div class="relative">
          <img
            v-if="selectedGoal"
            :src="`/sustainable-development-goals/icons/${selectedGoal.icon}`"
            class="odd-icon absolute z-10 h-14 rounded-2xl md:h-24"
            :alt="`Icône de l'objectif de développement durable n° ${selectedGoal.index}`"
          />
          <img
            :key="selectedGoal.index"
            :src="images[selectedGoal.index]"
            :alt="`Image de l'objectif de développement durable n° ${selectedGoal.index}`"
            class="motion-preset-slide-left-sm rounded-2xl"
          />
        </div>
      </div>
    </div>
  </Container>
</template>

<script setup lang="ts">
import Goal2Url from './assets/goal-2.webp';
import Goal12Url from './assets/goal-12.webp';
import Goal11Url from './assets/goal-11.webp';
import { computed, defineAsyncComponent, ref } from 'vue';
import { useSustainableDevelopmentGoals } from '@lychen/vue-sustainable-development-goals/composables/useSustainableDevelopmentGoals';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

const GoalSubSection = defineAsyncComponent(
  () => import('@/views/home/component/GoalSubSection.vue'),
);

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = usePrefixedI18n(CONFIG);

const { two, eleven, twelve } = useSustainableDevelopmentGoals();

const goals = computed(() => [eleven.value, two.value, twelve.value]);
const selectedGoal = ref(eleven.value);

const images: { [key: number]: string } = {
  2: Goal2Url,
  11: Goal11Url,
  12: Goal12Url,
};
</script>

<style scoped>
.odd-icon {
  top: -28px;
  left: -28px;
}
</style>
