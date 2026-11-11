import { describe, expect, it } from 'vitest';
import { oneOf, optional, required, toCollection, toDate, withoutUndefined } from './mapping';

describe('mapping', () => {
  it('turns the API null into undefined, and keeps any other value', () => {
    expect(optional(null)).toBeUndefined();
    expect(optional(0)).toBe(0);
    expect(optional('')).toBe('');
  });

  it('rejects a response that lacks a required value', () => {
    expect(required('0198', 'uuid')).toBe('0198');
    expect(() => required(null, 'area proposal uuid')).toThrow(
      'The API response has no area proposal uuid.',
    );
  });

  it('parses the API date-times', () => {
    expect(toDate('2026-11-11T09:30:00+00:00')).toEqual(new Date('2026-11-11T09:30:00Z'));
    expect(toDate(null)).toBeUndefined();
  });

  it('only keeps the values the front knows', () => {
    const known = ['draft', 'published'] as const;
    expect(oneOf(known, 'published')).toBe('published');
    expect(oneOf(known, 'added_by_the_api_since')).toBeUndefined();
    expect(oneOf(known, null)).toBeUndefined();
  });

  it('maps every member of a collection page and tells whether there is another', () => {
    const page = { member: [{ n: 1 }, { n: 2 }], totalItems: 40, view: { next: '/api/x?page=2' } };
    expect(toCollection(page, ({ n }) => n * 10)).toEqual({
      items: [10, 20],
      totalItems: 40,
      hasNextPage: true,
    });
    expect(toCollection({ member: [{ n: 1 }] }, ({ n }) => n)).toEqual({
      items: [1],
      totalItems: 1,
      hasNextPage: false,
    });
  });

  it('drops the undefined properties, and only them', () => {
    expect(withoutUndefined({ title: 'A', city: undefined, altitude: 0, activities: [] })).toEqual({
      title: 'A',
      altitude: 0,
      activities: [],
    });
  });
});
