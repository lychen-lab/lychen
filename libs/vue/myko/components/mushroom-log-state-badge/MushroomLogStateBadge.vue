<template>
  <Badge
    v-if="state"
    :variant="mushroomLogStateVariant(state)"
  >
    {{ mushroomLogStateLabel(state) }}
  </Badge>
</template>

<script lang="ts" setup>
import type { MushroomLogState } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';
import { Badge } from '@lychen/vue-components-core/badge';

import { useMushroomLabels } from '../../composables/use-mushroom-labels/useMushroomLabels';
import { mushroomLogStateVariant } from './mushroomLogStateVariant';

/**
 * A log's state as a badge. Renders nothing for a state this front does not know (`undefined`).
 */
defineProps<{ state?: MushroomLogState }>();

const { mushroomLogStateLabel } = useMushroomLabels();
</script>
