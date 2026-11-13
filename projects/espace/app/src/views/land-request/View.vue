<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
      <Button
        icon-only
        variant="ghost"
        size="sm"
        :label="tApp('back')"
        @click="goBack"
      >
        <template #icon>
          <IconArrowLeft />
        </template>
      </Button>
      <Button
        v-if="request"
        icon-only
        variant="ghost"
        size="sm"
        :label="tApp('share.action')"
        @click="share(request.title ?? '')"
      >
        <template #icon>
          <IconShare2 />
        </template>
      </Button>
    </div>

    <QueryState
      :is-pending="isPending"
      :is-error="isError"
      :error-text="isNotFound ? t('not_found') : t('error')"
      @retry="refetch()"
    >
      <article
        v-if="request"
        class="flex flex-col gap-6"
      >
        <header class="flex flex-col gap-2">
          <!-- Only its requester reads a request that is not active. -->
          <p
            v-if="request.state !== AREA_REQUEST_STATE.Active"
            class="self-start rounded-full bg-warning px-3 py-1 text-xs font-medium text-on-warning"
          >
            {{ t('not_published') }}
          </p>
          <h1 class="text-2xl leading-tight font-bold">{{ request.title }}</h1>
          <p
            v-if="request.city"
            class="flex items-center gap-1.5 text-sm text-on-surface/60"
          >
            <IconMapPin class="size-4 shrink-0" />
            {{ request.city }}
          </p>
        </header>

        <p
          v-if="request.minimalSurfaceRequested != null"
          class="flex items-center gap-3 rounded-2xl bg-surface-container p-4 text-sm"
        >
          <IconSquareDashed class="size-5 shrink-0 text-lime-700 dark:text-lime-200" />
          {{
            t('minimal_surface', { surface: n(request.minimalSurfaceRequested, 'square-meter') })
          }}
        </p>

        <section class="flex flex-col gap-2">
          <h2 class="font-semibold">{{ t('about') }}</h2>
          <p class="text-sm leading-relaxed whitespace-pre-line text-on-surface/70">
            {{ request.description }}
          </p>
        </section>

        <section
          v-if="request.activityCodes.length"
          class="flex flex-col gap-3"
        >
          <h2 class="font-semibold">{{ t('activities') }}</h2>
          <ul class="flex flex-wrap gap-2">
            <li
              v-for="code in request.activityCodes"
              :key="code"
              class="rounded-full bg-lime-50 px-3 py-1 text-xs font-medium text-lime-800"
            >
              {{ activityLabel(code) }}
            </li>
          </ul>
        </section>

        <footer class="flex items-center gap-3 rounded-2xl bg-surface-container p-4">
          <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-lime-100">
            <IconUser class="size-5 text-lime-700" />
          </div>
          <div class="flex flex-col gap-0.5 text-sm">
            <p
              v-if="requesterName"
              class="font-semibold"
            >
              {{ t('requested_by', { name: requesterName }) }}
            </p>
            <p
              v-if="request.createdAt"
              class="text-xs text-on-surface/60"
            >
              {{ t('created_at', { date: d(request.createdAt, 'short') }) }}
            </p>
          </div>
        </footer>
      </article>
    </QueryState>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { AREA_REQUEST_STATE } from '@lychen/typescript-front-model-business/area/AreaRequest';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useGetAreaRequest } from '@lychen/vue-espace-api/endpoint/area-request/get-area-request';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconArrowLeft from '@lychen/vue-icons/IconArrowLeft.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import IconShare2 from '@lychen/vue-icons/IconShare2.vue';
import IconSquareDashed from '@lychen/vue-icons/IconSquareDashed.vue';
import IconUser from '@lychen/vue-icons/IconUser.vue';
import QueryState from '@/components/QueryState.vue';
import { useActivities } from '@/composables/useActivities';
import { useShare } from '@/composables/useShare';
import { fullName } from '@/helpers/cards';
import { isNotFoundError } from '@/helpers/errors';
import { useAppI18n } from '@/i18n';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t, n, d } = useI18nExtended({
  messages: MESSAGES,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});
const { t: tApp } = useAppI18n();

const route = useRoute();
const router = useRouter();
const uuid = computed(() => String(route.params.uuid));

const { activityLabel } = useActivities();
const { share } = useShare();

// No live updates here: the API only publishes area proposals on Mercure.
const {
  data: request,
  isPending,
  isError,
  error,
  refetch,
} = useGetAreaRequest(uuid, {
  // A request that does not exist, or is not visible, will not appear by asking again.
  retry: (failureCount, failure) => !isNotFoundError(failure) && failureCount < 3,
});

const isNotFound = computed(() => isNotFoundError(error.value));
const requesterName = computed(() => fullName(request.value?.requester));

// Opened from a shared link, there is no previous screen to go back to.
function goBack() {
  if (window.history.state?.back) {
    router.back();
  } else {
    router.replace({ name: ROUTE_LAND_REQUESTS.name });
  }
}
</script>
