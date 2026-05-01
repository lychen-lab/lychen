<template>
  <Container class="flex flex-col items-center gap-4 pt-20">
    <Title variant="h1">{{ t('meta.title') }}</Title>
    <Paragraph class="text-center opacity-80">{{ t('meta.description') }}</Paragraph>
    <p class="text-xs font-light italic opacity-60">
      {{ t('document_change') }}
      <a
        href="https://github.com/lychen-lab/lychen/tree/main/projects/website/src/views/charter"
        target="_blank"
        class="underline"
        >github.com</a
      >
    </p>
  </Container>
  <Container class="flex flex-col gap-8 md:gap-24 md:pt-0">
    <CharterSection i18n-key="charter.section.human">
      <CharterArticle i18n-key="charter.article.absolute_inclusion" />
      <CharterArticle i18n-key="charter.article.asynchronous_interactions" />
      <CharterArticle i18n-key="charter.article.single_source_of_truth_ssot" />
      <CharterArticle i18n-key="charter.article.orbital_deadlines" />
      <CharterArticle i18n-key="charter.article.collective_mutual_intelligence" />
      <CharterArticle i18n-key="charter.article.apolitical" />
      <CharterArticle i18n-key="charter.article.egoless_contribution" />
      <CharterArticle i18n-key="charter.article.emergent_coordination" />
      <CharterArticle i18n-key="charter.article.actors_not_employees" />
      <CharterArticle i18n-key="charter.article.architecture_remembers" />
      <CharterArticle i18n-key="charter.article.you_choose_your_work" />
      <CharterArticle i18n-key="charter.article.your_time_your_rhythm" />
      <CharterArticle i18n-key="charter.article.your_ideas_matter" />
      <CharterArticle i18n-key="charter.article.considering_the_living" />
    </CharterSection>

    <CharterSection i18n-key="charter.section.software_technologies">
      <CharterArticle i18n-key="charter.article.open_source_software_oss" />
      <CharterArticle i18n-key="charter.article.not_hype_driven" />
      <CharterArticle i18n-key="charter.article.creating_beautiful_applications" />
      <CharterArticle i18n-key="charter.article.fully_integrable" />
      <CharterArticle i18n-key="charter.article.self_hosted_cloud_objective" />
    </CharterSection>

    <CharterSection i18n-key="charter.section.data">
      <CharterArticle i18n-key="charter.article.data_ownership" />
      <CharterArticle i18n-key="charter.article.no_advertising_resale" />
      <CharterArticle i18n-key="charter.article.encrypt_whats_important" />
    </CharterSection>

    <CharterSection i18n-key="charter.section.finance">
      <CharterArticle i18n-key="charter.article.sponsor_rather_than_investor" />
      <CharterArticle i18n-key="charter.article.transparent_finance" />
      <CharterArticle i18n-key="charter.article.fair_pricing_model" />
      <CharterArticle i18n-key="charter.article.profit_as_a_means_not_an_end" />
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
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';

const { t } = usePrefixedI18n(CONFIG);

useExtendedHead(t);
</script>
