import {
  MUSHROOM_LOG_STATE,
  type MushroomLogState,
} from '@lychen/typescript-front-model-business/mushroom/MushroomLog';
import type { VariantKey } from '@lychen/vue-components-core/badge';

const VARIANT_BY_STATE: Record<MushroomLogState, VariantKey> = {
  [MUSHROOM_LOG_STATE.Prepared]: 'outline',
  [MUSHROOM_LOG_STATE.Incubating]: 'tertiary',
  [MUSHROOM_LOG_STATE.Fruiting]: 'positive',
  [MUSHROOM_LOG_STATE.Retired]: 'default',
};

/** The colour of a log's state: quiet when it is waiting or retired, green when it produces. */
export function mushroomLogStateVariant(state: MushroomLogState): VariantKey {
  return VARIANT_BY_STATE[state];
}
