import { defineBreadcrumb, defineWebPage, useSchemaOrg } from '@unhead/schema-org/vue';
import { useRoute, useRouter } from 'vue-router';

export interface WebPageSchemaBreadcrumbItem {
  /** Visible label for the breadcrumb entry. */
  name: string;
  /** Absolute-from-root path of the entry (e.g. `/mission`). Omit for the current page. */
  item?: string;
}

export interface WebPageSchemaOptions {
  /** WebPage name. Defaults to the `meta.title` translation. */
  name?: string;
  /** WebPage description. Defaults to the `meta.description` translation. */
  description?: string;
  /**
   * Site/brand name used as the breadcrumb root when an explicit `breadcrumb`
   * is not provided. When omitted, no breadcrumb is emitted.
   */
  siteName?: string;
  /**
   * Explicit breadcrumb trail ordered from the site root to the current page.
   * Overrides the trail generated from `siteName`.
   */
  breadcrumb?: WebPageSchemaBreadcrumbItem[];
}

/**
 * Emit per-page schema.org structured data (`WebPage`, and optionally a
 * `BreadcrumbList`) so search engines can index each page with its own title,
 * description and breadcrumb trail. It complements the site-wide `Organization`
 * / `WebSite` graph declared once at the application root.
 *
 * Mirrors `useExtendedHead`: pass the prefixed `t` from `usePrefixedI18n` so the
 * page reuses its existing `meta.title` / `meta.description` keys.
 */
export function useWebPageSchema(t: (key: string) => string, options: WebPageSchemaOptions = {}) {
  const route = useRoute();
  const router = useRouter();

  const host = import.meta.env.VITE_UNHEAD_HOST;
  const toAbsolute = (path: string) => `https://${host}${path}`;

  const name = options.name ?? t('meta.title');
  const description = options.description ?? t('meta.description');

  useSchemaOrg([defineWebPage({ name, description })]);

  let breadcrumb = options.breadcrumb;
  if (!breadcrumb && options.siteName) {
    const canonicalPath = router.resolve(route.name ? { name: route.name } : route).path;
    breadcrumb = [
      { name: options.siteName, item: '/' },
      { name, item: canonicalPath },
    ];
  }

  if (breadcrumb && breadcrumb.length > 0) {
    useSchemaOrg([
      defineBreadcrumb({
        itemListElement: breadcrumb.map((entry) => ({
          name: entry.name,
          ...(entry.item ? { item: toAbsolute(entry.item) } : {}),
        })),
      }),
    ]);
  }
}
