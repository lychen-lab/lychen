import { toValue, watchEffect, type MaybeRefOrGetter } from 'vue';

import { APP_NAME } from '@lychen/typescript-myko/constants/App';

/**
 * The title of the browser tab, the history and the screen reader's page announcement: the screen's
 * own title, then the app (`Bûches · Myko`). Without a title of its own, or with the app's name as
 * its title, it is the app's name alone.
 */
export function formatDocumentTitle(screenTitle?: string | null): string {
  const title = screenTitle?.trim();

  return title && title !== APP_NAME ? `${title} · ${APP_NAME}` : APP_NAME;
}

/**
 * Titles the page for as long as the screen is there (WCAG 2.4.2, "Page Titled": a single-page app
 * has to do it itself on every route). Reactive: a getter or a ref follows the language or the data
 * (`useDocumentTitle(() => log.value?.label)`). The next screen sets its own title, so nothing is
 * restored when this one goes.
 */
export function useDocumentTitle(screenTitle: MaybeRefOrGetter<string | null | undefined>): void {
  watchEffect(() => {
    if (typeof document !== 'undefined') {
      document.title = formatDocumentTitle(toValue(screenTitle));
    }
  });
}
