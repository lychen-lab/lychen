/**
 * A harvested weight as a person reads it: grams below a kilo, then kilos with one decimal ("850 g",
 * "1,2 kg", "12,0 kg" in French), so that a column of weights lines up. `grams` is what the API
 * gives; a weight that is not a finite number gives an empty string.
 */
export function formatHarvestWeight(grams: number | undefined, locale: string): string {
  if (grams === undefined || !Number.isFinite(grams)) {
    return '';
  }

  if (Math.abs(grams) < 1000) {
    return `${new Intl.NumberFormat(locale, { maximumFractionDigits: 0 }).format(grams)} g`;
  }

  const kilograms = new Intl.NumberFormat(locale, {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1,
  }).format(grams / 1000);
  return `${kilograms} kg`;
}
