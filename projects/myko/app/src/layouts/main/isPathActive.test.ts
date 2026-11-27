import { describe, expect, it } from 'vitest';

import { isPathActive } from './isPathActive';

describe('isPathActive', () => {
  it('is active on the path itself and below it', () => {
    expect(isPathActive('/logs', '/logs')).toBe(true);
    expect(isPathActive('/logs/new', '/logs')).toBe(true);
    expect(isPathActive('/logs/1f4c/edit', '/logs')).toBe(true);
  });

  it('is not active on a path that only starts with the same letters', () => {
    expect(isPathActive('/logsheet', '/logs')).toBe(false);
    expect(isPathActive('/zones', '/logs')).toBe(false);
  });

  it('only matches "/" exactly', () => {
    expect(isPathActive('/', '/')).toBe(true);
    expect(isPathActive('/logs', '/')).toBe(false);
  });
});
