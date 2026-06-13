import { type ApplicationAlias } from '@lychen/typescript-applications/constants/ApplicationAlias';
import { computed, type ComputedRef } from 'vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { APPLICATION_ALIAS } from '@lychen/typescript-applications/constants/ApplicationAlias';
import { APPLICATIONS } from '../constants/Applications';
import { CONFIG } from '../i18n';

export type TranslatedApplication = {
  title: string;
  description: string;
  state: string;
  alias: ApplicationAlias;
  url?: string;
};

export function useApplicationsCatalog(): {
  applicationsList: ComputedRef<TranslatedApplication[]>;
  titleSortedApplicationsList: ComputedRef<TranslatedApplication[]>;
  opiniatedApplicationsList: ComputedRef<TranslatedApplication[]>;
  getAppInfo: (alias: ApplicationAlias) => TranslatedApplication;
} {
  const { t } = usePrefixedI18n(CONFIG);

  function generateAppInfo(alias: ApplicationAlias): TranslatedApplication {
    return {
      title: t(`${alias}.name`),
      description: t(`${alias}.description`),
      state: t(`state.${APPLICATIONS[alias].state}`),
      alias,
      url: APPLICATIONS[alias].url,
    };
  }

  const applicationsList = computed<TranslatedApplication[]>(() => {
    return Object.values(APPLICATION_ALIAS).map((alias) => {
      return generateAppInfo(alias);
    });
  });

  const titleSortedApplicationsList = computed<TranslatedApplication[]>(() => {
    return applicationsList.value.sort((a, b) =>
      a.title.toLowerCase().localeCompare(b.title.toLowerCase()),
    );
  });

  const opiniatedApplicationsList = computed<TranslatedApplication[]>(() => {
    const customOrder = [
      APPLICATION_ALIAS.Espace,
      APPLICATION_ALIAS.Tera,
      APPLICATION_ALIAS.Myko,
      APPLICATION_ALIAS.Meli,
      APPLICATION_ALIAS.Kiro,
      APPLICATION_ALIAS.Humu,
      APPLICATION_ALIAS.Novi,
      APPLICATION_ALIAS.Vara,
      APPLICATION_ALIAS.Kolo,
      APPLICATION_ALIAS.Robust,
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
