import { computed } from 'vue';
import { useGetAreaActivities } from '@lychen/vue-espace-api/endpoint/area-activity/get-area-activities';
import { TRANSLATION_KEY, useAppI18n } from '@/i18n';

/**
 * The activities of the API (`/api/area_activities`), which only knows them by code: the
 * labels are translations of the known codes, and an unknown code is shown as it is.
 */
export function useActivities() {
  const { t, te } = useAppI18n();

  const { data } = useGetAreaActivities({ pagination: false }, { staleTime: Infinity });

  function activityLabel(code: string): string {
    return te(`${TRANSLATION_KEY}.activities.${code}`) ? t(`activities.${code}`) : code;
  }

  const activities = computed(() =>
    (data.value?.member ?? [])
      .flatMap(({ code }) => (code ? [{ code, label: activityLabel(code) }] : []))
      .sort((a, b) => a.label.localeCompare(b.label)),
  );

  return { activities, activityLabel };
}
