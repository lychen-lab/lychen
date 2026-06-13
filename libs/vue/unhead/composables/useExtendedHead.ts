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

  // Crawlers (Open Graph, Twitter) require absolute URLs for images.
  function resolveAbsoluteUrl(url: string): string {
    return url.startsWith('http') ? url : `https://${host}${url}`;
  }

  function resolveLocalizedUrl(locale: string): string {
    if (route.name) {
      return `https://${host}${router.resolve({ name: route.name, params: { ...route.params, locale } }).path}`;
    }
    const localePattern = new RegExp(`^/(${AVAILABLE_LOCALES.join('|')})(/|$)`);
    return `https://${host}${route.path.replace(localePattern, `/${locale}$2`)}`;
  }

  const canonicalUrl =
    options?.canonical ??
    `https://${host}${router.resolve(route.name ? { name: route.name } : route).path}`;

  useHead({
    link: [
      {
        rel: 'canonical',
        href: canonicalUrl,
      },
      ...AVAILABLE_LOCALES.map(
        (locale: string) =>
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
    ogUrl: canonicalUrl,
    ogImage: options?.ogImage ? resolveAbsoluteUrl(options.ogImage) : undefined,
    twitterCard: 'summary_large_image',
    twitterTitle: t('meta.title'),
    twitterDescription: t('meta.description'),
  });
}
