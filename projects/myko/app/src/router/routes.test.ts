import { createMemoryHistory, createRouter } from 'vue-router';
import { describe, expect, it } from 'vitest';

import { MESSAGES } from '@/views/provisional/i18n';
import { PROVISIONAL_ROUTES } from '@/views/provisional';

import { ROUTE_NAME } from './routeNames';
import routes from './routes';

function createTestRouter() {
  return createRouter({ history: createMemoryHistory(), routes });
}

// The paths of projects/myko/CLAUDE.md, "App conventions": the QR entry point is tested apart.
const PATHS: Record<string, string> = {
  '/': ROUTE_NAME.Home,
  '/more': ROUTE_NAME.More,
  '/logs': ROUTE_NAME.Logs,
  '/logs/new': ROUTE_NAME.LogNew,
  '/logs/new-batch': ROUTE_NAME.LogNewBatch,
  '/logs/6a1c': ROUTE_NAME.LogDetail,
  '/logs/6a1c/edit': ROUTE_NAME.LogEdit,
  '/zones': ROUTE_NAME.Zones,
  '/zones/new': ROUTE_NAME.ZoneNew,
  '/zones/6a1c/edit': ROUTE_NAME.ZoneEdit,
  '/strains': ROUTE_NAME.Strains,
  '/strains/new': ROUTE_NAME.StrainNew,
  '/strains/6a1c/edit': ROUTE_NAME.StrainEdit,
  '/labels': ROUTE_NAME.Labels,
  '/scan': ROUTE_NAME.Scan,
  '/stats': ROUTE_NAME.Stats,
};

describe('routes', () => {
  it.each(Object.entries(PATHS))('%s is the %s screen', (path, name) => {
    expect(createTestRouter().resolve(path).name).toBe(name);
  });

  it('keeps "new" and "new-batch" out of the log uuid', () => {
    const router = createTestRouter();

    expect(router.resolve('/logs/new').params).toEqual({});
    expect(router.resolve('/logs/new-batch').params).toEqual({});
    expect(router.resolve('/logs/6a1c').params).toEqual({ uuid: '6a1c' });
  });

  it('forwards a label QR code, /l/<uuid>, to the log page', async () => {
    const router = createTestRouter();

    await router.push('/l/6a1c-qr');

    expect(router.currentRoute.value.fullPath).toBe('/logs/6a1c-qr');
    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.LogDetail);
  });

  it('sends an unknown path to the home screen', async () => {
    const router = createTestRouter();

    await router.push('/nowhere/at/all');

    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.Home);
  });

  it.each(Object.entries(MESSAGES).map(([locale, messages]) => [locale, messages.titles]))(
    'gives every provisional screen a title in %s',
    (_locale, titles) => {
      for (const route of PROVISIONAL_ROUTES) {
        expect(titles[route.name]).toBeTruthy();
      }
    },
  );
});
