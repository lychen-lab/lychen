import { CONFIG } from '../i18n';
import { type ApplicationAlias } from '@lychen/typescript-applications/constants/ApplicationAlias';
import { type Application } from '@lychen/typescript-applications/model/Application';
import { computed } from 'vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { APPLICATION_STATE } from '@lychen/typescript-applications/constants/ApplicationState';
import { APPLICATION_ALIAS } from '@lychen/typescript-applications/constants/ApplicationAlias';

export function useApplicationsCatalog() {
  const { t } = usePrefixedI18n(CONFIG);

  function generateAppInfo(alias: ApplicationAlias): Application {
    return {
      link: `https://${alias}.lychen.org`,
      title: t(`${alias}.name`),
      description: t(`${alias}.description`),
      state: APPLICATION_STATE.Funding,
      alias,
    };
  }

  const applicationsList = computed<Application[]>(() => {
    return Object.values(APPLICATION_ALIAS).map((alias) => {
      return generateAppInfo(alias);
    });
  });

  const titleSortedApplicationsList = computed<Application[]>(() => {
    return applicationsList.value.sort((a, b) =>
      a.title.toLowerCase().localeCompare(b.title.toLowerCase()),
    );
  });

  const opiniatedApplicationsList = computed<Application[]>(() => {
    const customOrder = [
      APPLICATION_ALIAS.Tera,
      APPLICATION_ALIAS.Myko,
      APPLICATION_ALIAS.Meli,
      APPLICATION_ALIAS.Kiro,
      APPLICATION_ALIAS.Humu,
      APPLICATION_ALIAS.Novi,
      APPLICATION_ALIAS.Vara,
      APPLICATION_ALIAS.Kolo,
    ];
    return Object.values(customOrder).map((alias) => {
      return generateAppInfo(alias);
    });
  });

  return {
    applicationsList,
    titleSortedApplicationsList,
    opiniatedApplicationsList,
    getAppInfo: generateAppInfo,
  };
}
