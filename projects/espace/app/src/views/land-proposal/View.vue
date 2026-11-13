<template>
  <div class="flex flex-col">
    <!-- Full-bleed illustration, with the controls that must stay reachable in every state -->
    <div class="relative -mx-4 -mt-4 aspect-4/3 w-screen">
      <img
        :src="AREA_PLACEHOLDER_IMAGE"
        :alt="tApp('image_alt')"
        class="size-full object-cover"
        decoding="async"
      />
      <div class="absolute inset-x-0 top-0 flex items-center justify-between p-4">
        <button
          :class="CONTROL"
          :aria-label="tApp('back')"
          @click="goBack"
        >
          <IconArrowLeft class="size-5" />
        </button>
        <div
          v-if="proposal"
          class="flex gap-2"
        >
          <button
            :class="CONTROL"
            :aria-label="tApp('share.action')"
            @click="share(proposal.title ?? '')"
          >
            <IconShare2 class="size-5" />
          </button>
          <button
            :class="[CONTROL, favorite ? 'text-red-400' : '']"
            :aria-label="favorite ? tApp('favorite.remove') : tApp('favorite.add')"
            :aria-pressed="favorite"
            @click="toggleFavorite(uuid)"
          >
            <IconHeartFilled
              v-if="favorite"
              class="size-5"
            />
            <IconHeart
              v-else
              class="size-5"
            />
          </button>
        </div>
      </div>
    </div>

    <QueryState
      :is-pending="isPending"
      :is-error="isError"
      :error-text="isNotFound ? t('not_found') : t('error')"
      @retry="refetch()"
    >
      <article
        v-if="proposal"
        class="flex flex-col gap-6 pt-5"
      >
        <header class="flex flex-col gap-2">
          <!-- Only its proposer reads a proposal that is not published. -->
          <p
            v-if="proposal.state !== AREA_PROPOSAL_STATE.Published"
            class="self-start rounded-full bg-warning px-3 py-1 text-xs font-medium text-on-warning"
          >
            {{ t('not_published') }}
          </p>
          <h1 class="text-2xl leading-tight font-bold">{{ proposal.title }}</h1>
          <p
            v-if="proposal.city"
            class="flex items-center gap-1.5 text-sm text-on-surface/60"
          >
            <IconMapPin class="size-4 shrink-0" />
            {{ proposal.city }}
          </p>
        </header>

        <dl class="grid grid-cols-3 gap-2">
          <div
            v-if="proposal.surfaceToShare != null"
            :class="FIGURE"
          >
            <IconSquareDashed class="size-5 text-lime-700 dark:text-lime-200" />
            <dd class="text-sm font-medium">{{ n(proposal.surfaceToShare, 'square-meter') }}</dd>
            <dt class="text-xs text-on-surface/60">{{ t('surface_to_share') }}</dt>
          </div>
          <div
            v-if="proposal.surfaceTotal != null"
            :class="FIGURE"
          >
            <IconFence class="size-5 text-lime-700 dark:text-lime-200" />
            <dd class="text-sm font-medium">{{ n(proposal.surfaceTotal, 'square-meter') }}</dd>
            <dt class="text-xs text-on-surface/60">{{ t('surface_total') }}</dt>
          </div>
          <div
            v-if="proposal.altitude != null"
            :class="FIGURE"
          >
            <IconMountain class="size-5 text-lime-700 dark:text-lime-200" />
            <dd class="text-sm font-medium">{{ n(proposal.altitude, 'meter') }}</dd>
            <dt class="text-xs text-on-surface/60">{{ t('altitude') }}</dt>
          </div>
        </dl>

        <section class="flex flex-col gap-2">
          <h2 class="font-semibold">{{ t('about') }}</h2>
          <p class="text-sm leading-relaxed whitespace-pre-line text-on-surface/70">
            {{ proposal.description }}
          </p>
        </section>

        <section
          v-if="proposal.activityCodes.length"
          class="flex flex-col gap-3"
        >
          <h2 class="font-semibold">{{ t('activities') }}</h2>
          <ul class="flex flex-wrap gap-2">
            <li
              v-for="code in proposal.activityCodes"
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
              v-if="proposerName"
              class="font-semibold"
            >
              {{ t('proposed_by', { name: proposerName }) }}
            </p>
            <p
              v-if="proposal.createdAt"
              class="text-xs text-on-surface/60"
            >
              {{ t('created_at', { date: d(proposal.createdAt, 'short') }) }}
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
import { AREA_PROPOSAL_STATE } from '@lychen/typescript-front-model-business/area/AreaProposal';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useGetAreaProposal } from '@lychen/vue-espace-api/endpoint/area-proposal/get-area-proposal';
import IconArrowLeft from '@lychen/vue-icons/IconArrowLeft.vue';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import IconHeart from '@lychen/vue-icons/IconHeart.vue';
import IconHeartFilled from '@lychen/vue-icons/IconHeartFilled.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import IconShare2 from '@lychen/vue-icons/IconShare2.vue';
import IconSquareDashed from '@lychen/vue-icons/IconSquareDashed.vue';
import IconUser from '@lychen/vue-icons/IconUser.vue';
import QueryState from '@/components/QueryState.vue';
import { useActivities } from '@/composables/useActivities';
import { useFavorites } from '@/composables/useFavorites';
import { useShare } from '@/composables/useShare';
import { AREA_PLACEHOLDER_IMAGE, fullName } from '@/helpers/cards';
import { isNotFoundError } from '@/helpers/errors';
import { useAppI18n } from '@/i18n';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const CONTROL =
  'flex size-10 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-sm transition hover:bg-black/60';
const FIGURE = 'bg-surface-container flex flex-col items-center gap-1 rounded-2xl p-3 text-center';

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
const { isFavorite, toggleFavorite } = useFavorites();
const favorite = computed(() => isFavorite(uuid.value));

const {
  data: proposal,
  isPending,
  isError,
  error,
  refetch,
} = useGetAreaProposal(uuid, {
  // Live: anyone's edits of this proposal replace it on screen as they are made; a deletion
  // refetches it, and the query finds out.
  live: true,
  // A proposal that does not exist, or is not visible, will not appear by asking again.
  retry: (failureCount, failure) => !isNotFoundError(failure) && failureCount < 3,
});

const isNotFound = computed(() => isNotFoundError(error.value));
const proposerName = computed(() => fullName(proposal.value?.proposer));

// Opened from a shared link, there is no previous screen to go back to.
function goBack() {
  if (window.history.state?.back) {
    router.back();
  } else {
    router.replace({ name: ROUTE_LAND_PROPOSALS.name });
  }
}
</script>
