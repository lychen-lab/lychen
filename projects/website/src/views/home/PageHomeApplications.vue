<template>
  <Container class="flex flex-col items-center gap-4">
    <Title variant="h2">{{ t('applications.title') }}</Title>
    <Title
      variant="h2"
      class="text-center opacity-80"
      >{{ t('applications.second_title') }}</Title
    >
    <Dialog v-model:open="isOpen">
      <Carousel
        class="mt-10 w-[85%] sm:w-[90%]"
        :opts="{
          align: 'start',
        }"
      >
        <CarouselContent>
          <CarouselItem
            v-for="application in opiniatedApplicationsList"
            :key="application.title"
            class="basis-2/3 sm:basis-2/5 md:basis-2/5 lg:basis-2/7"
          >
            <DialogTrigger as-child>
              <ApplicationCard
                :application="application"
                background-image-folder="applications-covers"
                class="bg-surface-container min-h-[400px] cursor-pointer rounded-3xl p-6 md:min-h-[500px]"
                :data-umami-event="`Clicks on ${application.alias} card`"
                @click="selectedApplication = application"
              >
                <template #footer
                  ><Button
                    class="animate-in slide-in-from-bottom-4 z-10 justify-end self-center text-sm duration-300 md:hidden md:group-hover:flex"
                    size="sm"
                  >
                    {{ t('applications.see_features') }}
                  </Button></template
                >
              </ApplicationCard>
            </DialogTrigger>
          </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>
      <DialogContent class="max-h-dvh w-full gap-8 overflow-y-scroll md:max-w-[50%]">
        <div
          class="bg-secondary-container text-on-secondary-container flex flex-col items-stretch justify-between gap-4 overflow-y-auto rounded-3xl p-4 md:p-6"
        >
          <div class="flex flex-col gap-2">
            <div class="flex flex-row items-center justify-between">
              <ApplicationTitle
                class="text-3xl"
                :value="selectedApplication.title"
              />
              <DialogClose />
            </div>

            <Paragraph>{{ selectedApplication.description }}</Paragraph>
          </div>
        </div>
        <ApplicationsGridFeatures :features="features" />
      </DialogContent>
    </Dialog>
  </Container>
</template>

<script setup lang="ts">
import { defineAsyncComponent, onBeforeUnmount, ref, watch } from 'vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useApplicationsCatalog } from '@lychen/vue-applications/composables/useApplicationsCatalog';
import ApplicationCard from '@lychen/vue-applications/components/ApplicationCard.vue';
import ApplicationTitle from '@lychen/vue-applications/components/ApplicationTitle.vue';
import Carousel from '@lychen/vue-components-core/carousel/Carousel.vue';
import CarouselItem from '@lychen/vue-components-core/carousel/CarouselItem.vue';
import CarouselPrevious from '@lychen/vue-components-core/carousel/CarouselPrevious.vue';
import CarouselNext from '@lychen/vue-components-core/carousel/CarouselNext.vue';
import CarouselContent from '@lychen/vue-components-core/carousel/CarouselContent.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import DialogTrigger from '@lychen/vue-components-core/dialog/DialogTrigger.vue';
import { useApplicationsFeatures } from '@lychen/vue-applications/composables/useApplicationsFeatures';
import DialogClose from '@lychen/vue-components-core/dialog/DialogClose.vue';
import IconLink from '@lychen/vue-icons/IconLink.vue';
import ApplicationsGridFeatures from '@lychen/vue-applications/components/grids/ApplicationsGridFeatures.vue';

const Dialog = defineAsyncComponent(() => import('@lychen/vue-components-core/dialog/Dialog.vue'));

const DialogContent = defineAsyncComponent(
  () => import('@lychen/vue-components-core/dialog/DialogContent.vue'),
);

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { opiniatedApplicationsList } = useApplicationsCatalog();
const { getFeaturesOrganizedByGroup } = useApplicationsFeatures();

const selectedApplication = ref();
const features = ref();

const unwatch = watch(selectedApplication, () => {
  if (selectedApplication.value) {
    features.value = getFeaturesOrganizedByGroup(selectedApplication.value.alias);
  }
});

const isOpen = ref(false);

onBeforeUnmount(() => {
  unwatch();
});
</script>
