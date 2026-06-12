import { useHead, useSeoMeta } from '@unhead/vue';
import { useRoute, useRouter } from 'vue-router';
import { AVAILABLE_LOCALES, defaultLocale } from '@lychen/vue-i18n/configs/Default';

export function useExtendedHead(
  t: (key: string) => string,
  options?: { ogImage?: string; canonical?: string },
) {
  const router = useRouter();
  const route = useRoute();

  const host = import.meta.env.VITE_UNHEAD_HOST;

  function resolveLocalizedUrl(locale: string): string {
    if (route.name) {
      return `https://${host}${router.resolve({ name: route.name, params: { ...route.params, locale } }).path}`;
    }
    const localePattern = new RegExp(`^/(${AVAILABLE_LOCALES.join('|')})(/|$)`);
    return `https://${host}${route.path.replace(localePattern, `/${locale}$2`)}`;
  }

  useHead({
    link: [
      {
        rel: 'canonical',
        href:
          options?.canonical ??
          `https://${host}${router.resolve(route.name ? { name: route.name } : route).path}`,
      },
      ...AVAILABLE_LOCALES.map(
        (locale) =>
          ({
            rel: 'alternate' as const,
            hreflang: locale,
            href: resolveLocalizedUrl(locale),
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
          }) as any,
      ),
      {
        rel: 'alternate' as const,
        hreflang: 'x-default',
        href: resolveLocalizedUrl(defaultLocale),
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
      } as any,
    ],
  });

  useSeoMeta({
    title: t('meta.title'),
    description: t('meta.description'),
    ogDescription: t('meta.description'),
    ogTitle: t('meta.title'),
    ogImage: options?.ogImage,
    twitterCard: 'summary_large_image',
    twitterTitle: t('meta.title'),
    twitterDescription: t('meta.description'),
  });
}
