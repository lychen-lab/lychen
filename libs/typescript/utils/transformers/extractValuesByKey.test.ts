import { describe, expect, it } from 'vitest';
import { extractValuesByKey } from './extractValuesByKey';

interface Plant {
  id: number;
  name: string;
  family?: string;
}

describe('extractValuesByKey', () => {
  it('extracts the values of the given key from every item', () => {
    const plants: Plant[] = [
      { id: 1, name: 'Oak', family: 'Fagaceae' },
      { id: 2, name: 'Clover', family: 'Fabaceae' },
    ];

    expect(extractValuesByKey(plants, 'name')).toEqual(['Oak', 'Clover']);
    expect(extractValuesByKey(plants, 'id')).toEqual([1, 2]);
  });

  it('returns an empty array for an empty list', () => {
    expect(extractValuesByKey([] as Plant[], 'name')).toEqual([]);
  });

  it('preserves the order and duplicates of the source list', () => {
    const plants: Plant[] = [
      { id: 3, name: 'Moss' },
      { id: 1, name: 'Lichen' },
      { id: 3, name: 'Moss' },
    ];

    expect(extractValuesByKey(plants, 'id')).toEqual([3, 1, 3]);
  });

  it('keeps undefined slots for items missing an optional key', () => {
    const plants: Plant[] = [
      { id: 1, name: 'Oak', family: 'Fagaceae' },
      { id: 2, name: 'Moss' },
    ];

    expect(extractValuesByKey(plants, 'family')).toEqual(['Fagaceae', undefined]);
  });

  it('keeps references to extracted object values', () => {
    const coordinates = { lat: 45.76, lng: 4.83 };
    const list = [{ position: coordinates }];

    const result = extractValuesByKey(list, 'position');

    expect(result[0]).toBe(coordinates);
  });
});
