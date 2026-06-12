import { describe, expectTypeOf, it } from 'vitest';
import type { ObjectValues } from './ObjectValues';

const PLANT_STATUS = {
  Dormant: 'dormant',
  Growing: 'growing',
} as const;

describe('ObjectValues', () => {
  it('produces the union of the literal value types of a const object', () => {
    expectTypeOf<ObjectValues<typeof PLANT_STATUS>>().toEqualTypeOf<'dormant' | 'growing'>();
  });

  it('resolves to the value type of a uniform record', () => {
    expectTypeOf<ObjectValues<Record<string, number>>>().toEqualTypeOf<number>();
  });

  it('produces a union across heterogeneous value types', () => {
    expectTypeOf<ObjectValues<{ id: number; name: string }>>().toEqualTypeOf<number | string>();
  });
});
