import { describe, expect, it } from 'vitest';

import { MUSHROOM_LOG_STATE } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';

import { mushroomLogStateVariant } from './mushroomLogStateVariant';

describe('mushroomLogStateVariant', () => {
  it('colours each state of the lifecycle', () => {
    expect(mushroomLogStateVariant(MUSHROOM_LOG_STATE.Prepared)).toBe('outline');
    expect(mushroomLogStateVariant(MUSHROOM_LOG_STATE.Incubating)).toBe('tertiary');
    expect(mushroomLogStateVariant(MUSHROOM_LOG_STATE.Fruiting)).toBe('positive');
    expect(mushroomLogStateVariant(MUSHROOM_LOG_STATE.Retired)).toBe('default');
  });

  it('gives each state its own variant, so that two states are never mistaken', () => {
    const variants = Object.values(MUSHROOM_LOG_STATE).map(mushroomLogStateVariant);

    expect(new Set(variants).size).toBe(variants.length);
  });
});
