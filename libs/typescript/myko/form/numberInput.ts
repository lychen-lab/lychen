/**
 * Reads a number typed in a field (a weight in grams, a quantity of dowels) for the validation to
 * judge. Undefined for a blank field (the form says it is missing); NaN for text that is not a
 * plain number (`12 g`, `1e3`), so that it is refused as a wrong value, not as a missing one. A
 * decimal comma is read as a point, as phones in French type `12,5`. Whether the number is a whole
 * one, and at least 1, is the validation's to say, not this function's.
 */
export function parseNumberInput(text: string): number | undefined {
  const trimmed = text.trim();

  if (trimmed === '') {
    return undefined;
  }

  return /^[+-]?\d+([.,]\d+)?$/.test(trimmed) ? Number(trimmed.replace(',', '.')) : Number.NaN;
}
