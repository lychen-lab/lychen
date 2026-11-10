import { computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import { useEspaceApi } from '@lychen/vue-espace/composables/use-espace-api/useEspaceApi';
import { TRANSLATION_KEY, useAppI18n } from '@/i18n';

/**
 * The activities of the API (`/api/area_activities`), which only knows them by code: the
 * labels are translations of the known codes, and an unknown code is shown as it is.
 */
export function useActivities() {
  const { api } = useEspaceApi();
  const { t, te } = useAppI18n();

  const { data } = useQuery({
    queryKey: ['area-activities'],
    queryFn: async () => {
      const response = await api.apiAreaActivitiesGetCollection({ query: { pagination: false } });
      return response.data;
    },
    staleTime: Infinity,
  });

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
