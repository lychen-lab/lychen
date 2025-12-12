<template>
  <Container class="flex flex-col items-center gap-4 pt-20">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph>{{ t('description') }}</Paragraph>
  </Container>
  <Container class="flex flex-col gap-24 py-24">
    <CharterSection i18n-key="charter.human_section">
      <CharterArticle i18n-key="charter.absolute_inclusion" />
      <CharterArticle i18n-key="charter.asynchronous_interactions" />
      <CharterArticle i18n-key="charter.single_source_of_truth_ssot" />
      <CharterArticle i18n-key="charter.orbital_deadlines" />
      <CharterArticle i18n-key="charter.collective_mutual_intelligence_section" />
      <CharterArticle i18n-key="charter.apolitical_section" />
      <CharterArticle i18n-key="charter.egoless_contribution" />
      <CharterArticle i18n-key="charter.emergent_coordination_section" />
      <CharterArticle i18n-key="charter.actors_not_employees_section" />
      <CharterArticle i18n-key="charter.architecture_remembers_section" />
      <CharterArticle i18n-key="charter.you_choose_your_work" />
      <CharterArticle i18n-key="charter.your_time_your_rhythm" />
      <CharterArticle i18n-key="charter.your_ideas_matter" />
      <CharterArticle i18n-key="charter.considering_the_living" />
    </CharterSection>

    <CharterSection i18n-key="charter.software_technologies_section">
      <CharterArticle i18n-key="charter.open_source_software_oss" />
      <CharterArticle i18n-key="charter.not_hype_driven" />
      <CharterArticle i18n-key="charter.creating_beautiful_applications" />
      <CharterArticle i18n-key="charter.fully_integrable" />
      <CharterArticle i18n-key="charter.self_hosted_cloud_objective" />
    </CharterSection>

    <CharterSection i18n-key="charter.data_section">
      <CharterArticle i18n-key="charter.data_ownership" />
      <CharterArticle i18n-key="charter.no_advertising_resale_section" />
      <CharterArticle i18n-key="charter.encrypt_whats_important" />
    </CharterSection>

    <CharterSection i18n-key="charter.finance_section">
      <CharterArticle i18n-key="charter.sponsor_rather_than_investor" />
      <CharterArticle i18n-key="charter.transparent_finance_section" />
      <CharterArticle i18n-key="charter.fair_pricing_model_section" />
      <CharterArticle i18n-key="charter.profit_as_a_means_not_an_end" />
    </CharterSection>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import CharterSection from './CharterSection.vue';
import CharterArticle from './CharterArticle.vue';

const { t } = usePrefixedI18n(CONFIG);
</script>
