import { globSync } from 'node:fs';
import type { ViteSSGOptions } from 'vite-ssg';
import generateSitemap from 'vite-ssg-sitemap';
import type { RouteRecordRaw } from 'vue-router';
import { transformHtmlTemplate } from '@unhead/vue/server';

export const ssgOptions: ViteSSGOptions = {
  script: 'async',
  formatting: 'prettify',
  includedRoutes(paths: string[], routes: readonly RouteRecordRaw[]) {
    const locales = ['fr-FR', 'en-US'];
    return paths.flatMap((path) => {
      if (!path.includes(':locale?')) return path;
      return locales.map((locale) => path.replace(':locale?', locale));
    });
  },
  dirStyle: 'nested',
  // vite-ssg (≤28.x) still serializes heads through unhead v2 APIs, which
  // silently no-op on the unhead v3 instances our apps create: pages build
  // but title/canonical/og/JSON-LD never reach the HTML. Re-render the head
  // ourselves with the v3 server transformer (it extracts the template's own
  // tags, merges the app entries and re-applies everything, deduped).
  // Drop this hook once vite-ssg ships unhead v3 support.
  onPageRendered(_route, renderedHTML, appCtx) {
    if (!appCtx.head) return renderedHTML;
    // vite-ssg types `appCtx.head` as a v2 VueHeadClient, but at runtime the apps
    // create a v3 unhead instance — the exact input `transformHtmlTemplate` expects.
    const head = appCtx.head as unknown as Parameters<typeof transformHtmlTemplate>[0];
    return transformHtmlTemplate(head, renderedHTML);
  },
  onFinished() {
    // Fall back to the literal ${HOST} placeholder so a host-agnostic build can be
    // produced; the docker entrypoint substitutes it (envsubst) at container start.
    const host = process.env.VITE_UNHEAD_HOST || '${HOST}';
    generateSitemap({
      hostname: `https://${host}`,
      // The root only redirects to a locale (libs/caddy/configs/ssg/Caddyfile).
      exclude: ['/'],
      ...sitemapHints(),
    });
  },
};

// Legal pages, by their last path segment: they rarely change and should not
// compete with the content pages.
const LEGAL_PAGES = ['privacy-policy', 'terms-of-use'];

/**
 * vite-ssg-sitemap gives every URL `priority 1.0` and `changefreq daily` unless
 * told otherwise, which tells crawlers nothing. Rank the pages found in dist
 * instead: the locale home pages first, the legal pages last.
 */
function sitemapHints() {
  const priority: Record<string, number> = { '*': 0.7 };
  const changefreq: Record<string, string> = { '*': 'monthly' };

  for (const file of globSync('**/index.html', { cwd: 'dist' })) {
    // Normalised to the route keys vite-ssg-sitemap looks up, e.g. /fr-FR/privacy-policy
    const segments = file.split(/[\\/]/).slice(0, -1);
    const route = `/${segments.join('/')}`;

    if (segments.length === 1) {
      priority[route] = 1;
      changefreq[route] = 'weekly';
    } else if (LEGAL_PAGES.includes(segments.at(-1) ?? '')) {
      priority[route] = 0.3;
      changefreq[route] = 'yearly';
    }
  }

  return { priority, changefreq };
}
