<template>
  <SectionSetting :title="t('tabs.general.title')">
    <FormTeraLandUpdate
      v-if="land"
      :land="land"
    />
  </SectionSetting>
  <SectionSetting
    :title="t('tabs.general.danger_zone.title')"
    :description="t('tabs.general.danger_zone.description')"
  >
    <div class="flex flex-col justify-between gap-2 md:flex-row">
      <div>
        <BaseHeading variant="h3">
          {{ t('tabs.general.danger_zone.transfer.title') }}
        </BaseHeading>
        <p class="opacity-80">
          {{ t('tabs.general.danger_zone.transfer.description') }}
        </p>
      </div>
      <Button
        variant="ghost"
        class="border-negative text-negative border-1"
      >
        {{ t('tabs.general.danger_zone.transfer.button.label') }}
      </Button>
    </div>
    <div class="flex flex-col justify-between gap-2 md:flex-row">
      <div>
        <BaseHeading variant="h3">
          {{ t('tabs.general.danger_zone.delete.title') }}
        </BaseHeading>
        <p class="opacity-80">
          {{ t('tabs.general.danger_zone.delete.description') }}
        </p>
      </div>

      <DialogTeraLandDelete
        v-if="land"
        :land="land"
        ><Button
          variant="ghost"
          class="border-negative text-negative border-1"
        >
          {{ t('tabs.general.danger_zone.delete.button.label') }}
        </Button>
      </DialogTeraLandDelete>
    </div>
  </SectionSetting>
</template>

<script setup lang="ts">
import { BaseHeading } from '@lychen/vue-components-app/base-heading';
import { SectionSetting } from '@lychen/vue-components-app/section-setting';
import Button from '@lychen/vue-components-core/button/Button.vue';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import DialogTeraLandDelete from '@lychen/vue-tera/components/land/dialogs/delete/DialogTeraLandDelete.vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { inject } from 'vue';
import { INJECTION_KEY_LAND } from '@/layouts/land-layout';
import { useEventBus } from '@vueuse/core';
import { landDeleteSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import { useRouter } from 'vue-router';
import { RoutePageLands } from '@/pages/lands';
import FormTeraLandUpdate from '@lychen/vue-tera/components/land/forms/FormTeraLandUpdate.vue';

const land = inject(INJECTION_KEY_LAND);

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const router = useRouter();

const { on } = useEventBus(landDeleteSucceededEvent);

on(() => {
  router.push({ name: RoutePageLands.name });
});
</script>
