import type { FungusSpecies } from '@lychen/typescript-front-model-business/mushroom/FungusSpecies';
import type { WoodSpecies } from '@lychen/typescript-front-model-business/mushroom/WoodSpecies';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { TRANSLATION_KEY, messages } from './i18n';

type SpeciesGroup = 'fungus_species' | 'wood_species';
type SpeciesLike = Pick<FungusSpecies | WoodSpecies, 'code' | 'scientificName'>;

export interface UseSpeciesLabels {
  /** The fungus species' name in the current language. */
  fungusSpeciesLabel: (species?: Pick<FungusSpecies, 'code' | 'scientificName'> | null) => string;
  /** The wood species' name in the current language. */
  woodSpeciesLabel: (species?: Pick<WoodSpecies, 'code' | 'scientificName'> | null) => string;
}

/**
 * Names of the reference species: the API only gives a `code` and a scientific name, the app
 * translates the code. A code without a translation (a species added in the database after the
 * app shipped) falls back to the scientific name, then to the code itself, so that a list never
 * shows a hole nor a raw i18n key.
 */
export function useSpeciesLabels(): UseSpeciesLabels {
  const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY });

  function label(group: SpeciesGroup, species?: SpeciesLike | null): string {
    const fallback = species?.scientificName ?? species?.code ?? '';
    if (!species?.code) {
      return fallback;
    }

    const key = `${TRANSLATION_KEY}.${group}.${species.code}`;
    // `t` answers the key itself when there is no message, in the current language or its
    // fallback, which `te` would not tell.
    const translated = t(key, {}, { missingWarn: false, fallbackWarn: false });

    return translated === key ? fallback : translated;
  }

  return {
    fungusSpeciesLabel: (species) => label('fungus_species', species),
    woodSpeciesLabel: (species) => label('wood_species', species),
  };
}
