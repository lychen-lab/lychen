import { describe, expect, it } from 'vitest';
import { cn } from './Cn';

describe('cn', () => {
  it('returns an empty string when called without arguments', () => {
    expect(cn()).toBe('');
  });

  it('concatenates independent class names', () => {
    expect(cn('px-2', 'py-4')).toBe('px-2 py-4');
  });

  it('keeps the last class when Tailwind classes conflict', () => {
    expect(cn('p-2', 'p-4')).toBe('p-4');
    expect(cn('text-sm', 'text-lg')).toBe('text-lg');
  });

  it('does not merge classes from different Tailwind groups', () => {
    expect(cn('text-sm', 'text-red-500')).toBe('text-sm text-red-500');
  });

  it('ignores falsy values', () => {
    expect(cn('flex', undefined, null, false, '')).toBe('flex');
  });

  it('handles conditional classes provided as objects', () => {
    expect(cn({ flex: true, hidden: false }, 'gap-2')).toBe('flex gap-2');
  });

  it('flattens nested arrays of classes', () => {
    expect(cn(['flex', ['items-center', ['justify-between']]])).toBe(
      'flex items-center justify-between',
    );
  });

  it('resolves conflicts across mixed input shapes', () => {
    expect(cn('p-2', { 'p-4': true }, ['p-8'])).toBe('p-8');
  });
});
