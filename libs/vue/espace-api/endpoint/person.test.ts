import { describe, expect, it } from 'vitest';
import { toPersonName } from './person';

describe('toPersonName', () => {
  it('groups a name, or gives none when the API has none', () => {
    expect(toPersonName('Sophie', null)).toEqual({ firstName: 'Sophie', lastName: undefined });
    expect(toPersonName(null, null)).toBeUndefined();
  });
});
