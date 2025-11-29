import { messages, TRANSLATION_KEY } from '../i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { LINK } from '@lychen/typescript-constants/Link';

export function useResourcesMenu() {
  const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

  const resourcesMenuList = [
    {
      title: t(`navigation.resources.blog.title`),
      description: t(`navigation.resources.blog.description`),
      link: LINK.Blog,
    },
    /*{
        title: t(`navigation.resources.feedback.title`),
        description: t(`navigation.resources.feedback.description`),
        link: '',
      },
      {
        title: t(`navigation.resources.roadmap.title`),
        description: t(`navigation.resources.roadmap.description`),
        link: '',
      },
      {
        title: t(`navigation.resources.docs.title`),
        description: t(`navigation.resources.docs.description`),
        link: '',
      },*/
  ];

  return {
    resourcesMenuList,
  };
}
