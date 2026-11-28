import { resolveMushroomLogQrOrigin } from '@lychen/typescript-myko/qr/mushroomLogQrOrigin';

/**
 * The origin written in the QR codes of the labels: `VITE_MYKO_APP_URL` (`MYKO_APP_URL` in the
 * container) when it is set, else the origin the app runs on. Every screen that draws or prints a
 * QR code takes it from here, never from `window.location`: a label printed from a laptop or a
 * staging build must still open the real app (projects/myko/CLAUDE.md, "App conventions").
 */
export function getMushroomLogQrOrigin(): string {
  return resolveMushroomLogQrOrigin(import.meta.env.VITE_MYKO_APP_URL, window.location.origin);
}
