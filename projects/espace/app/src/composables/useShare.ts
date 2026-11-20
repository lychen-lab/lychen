import { toast } from '@lychen/vue-components-core/toast';
import { useAppI18n } from '@/i18n';

/**
 * Shares the current page: through the Web Share API where the browser has it (mobile,
 * mostly), by copying its link to the clipboard otherwise.
 */
export function useShare() {
  const { t } = useAppI18n();

  async function share(title: string) {
    const url = window.location.href;

    if (typeof navigator.share === 'function') {
      try {
        await navigator.share({ title, url });
        return;
      } catch (error) {
        // The user closed the share sheet: nothing to report, nothing to fall back to.
        if (error instanceof DOMException && error.name === 'AbortError') {
          return;
        }
      }
    }

    try {
      await navigator.clipboard.writeText(url);
      toast({ title: t('share.copied'), variant: 'positive' });
    } catch {
      toast({ title: t('share.failed'), variant: 'negative' });
    }
  }

  return { share };
}
