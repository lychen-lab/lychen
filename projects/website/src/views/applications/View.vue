<template>
  <Container class="flex flex-col items-center gap-8 pt-20">
    <Title
      variant="h2"
      class="text-center md:basis-2/5"
      >{{ t('applications.title') }}</Title
    >
    <div
      v-if="list"
      class="flex flex-col gap-4 md:grid md:grid-cols-3 md:gap-8"
    >
      <div
        v-for="application in list"
        :key="application.alias"
        class="border-on-surface/10 flex flex-col gap-2 rounded-3xl border p-4"
      >
        <div class="flex items-center justify-between gap-2">
          <Title
            variant="h3"
            class="lowercase"
            >{{ application.title }}</Title
          >
          <Badge>{{ application.state }}</Badge>
        </div>

        <Paragraph class="opacity-80">{{ application.description }}</Paragraph>
        <a
          v-if="application.alias === 'espace'"
          :href="'https://' + application.alias + '.lychen.org'"
          target="_blank"
          ><Button size="xs">Site web</Button></a
        >
      </div>
    </div>
  </Container>
  <Container class="flex flex-col items-start gap-4">
    <Title
      variant="h2"
      class="md:w-2/3"
      >{{ t('use_cases.title') }}</Title
    >
    <Paragraph
      variant="website-highlight"
      class="opacity-80 md:w-2/3"
    >
      {{ t('use_cases.description') }}
    </Paragraph>
    <LychenEcosystem />
  </Container>
  <Container class="flex flex-col items-center gap-4">
    <Title
      variant="h2"
      class="text-center md:basis-2/5"
      >{{ t('oss.title') }}</Title
    >
    <Paragraph
      variant="website-highlight"
      class="text-center opacity-90 md:w-3/5"
      >{{ t('oss.description') }}</Paragraph
    >
    <a
      :href="SOCIAL_LINK.GitHub"
      target="_blank"
      rel="noopener noreferrer"
    >
      <Button
        >{{ t('oss.button.label') }}
        <template #icon>
          <IconArrowUpRight />
        </template>
      </Button>
    </a>
  </Container>
  <Container>
    <BentoSummary />
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import { useApplicationsCatalog } from '@lychen/vue-applications/composables/useApplicationsCatalog';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';
import { SOCIAL_LINK } from '@lychen/typescript-constants/Social';
import Badge from '@lychen/vue-components-core/badge/Badge.vue';
import LychenEcosystem from '@lychen/vue-drawio-core/LychenEcosystem.vue';
import { BentoSummary } from '@/components/bento-summary';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { useWebPageSchema } from '@/composables/useWebPageSchema';

const { t } = usePrefixedI18n(CONFIG);

const { opiniatedApplicationsList: list } = useApplicationsCatalog();

useExtendedHead(t);
useWebPageSchema(t, { siteName: 'lychen' });
</script>
