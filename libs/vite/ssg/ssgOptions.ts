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
    generateSitemap({ hostname: `https://${host}` });
  },
};
