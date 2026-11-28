import { describe, expect, it } from 'vitest';

import { parseNumberInput } from './numberInput';

describe('parseNumberInput', () => {
  it('reads a whole number, with or without a decimal part that is zero', () => {
    expect(parseNumberInput('250')).toBe(250);
    expect(parseNumberInput(' 250 ')).toBe(250);
    expect(parseNumberInput('250.0')).toBe(250);
    expect(parseNumberInput('250,0')).toBe(250);
  });

  it('gives undefined for a blank field', () => {
    expect(parseNumberInput('')).toBeUndefined();
    expect(parseNumberInput('   ')).toBeUndefined();
  });

  it('gives NaN for what is not a plain number, so that it is refused as a wrong value', () => {
    expect(parseNumberInput('12 g')).toBeNaN();
    expect(parseNumberInput('1e3')).toBeNaN();
    expect(parseNumberInput('abc')).toBeNaN();
    expect(parseNumberInput('1,2,3')).toBeNaN();
  });

  it('keeps a decimal as it is (a whole number is for the validation to ask)', () => {
    expect(parseNumberInput('12,5')).toBe(12.5);
    expect(parseNumberInput('-5')).toBe(-5);
  });
});
