<template>
  <svg
    :viewBox="`0 0 ${qr.size} ${qr.size}`"
    role="img"
    :aria-label="title"
    shape-rendering="crispEdges"
    xmlns="http://www.w3.org/2000/svg"
  >
    <title>{{ title }}</title>
    <path
      :d="qr.path"
      fill="currentColor"
    />
  </svg>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { mushroomLogQrContent } from '@lychen/typescript-myko/qr/mushroomLogQr';
import { mushroomLogQrCode } from '@lychen/typescript-myko/qr/mushroomLogQrCode';

/**
 * The QR code of a log: the address `<app origin>/l/<log uuid>` of its fiche, drawn as an SVG that
 * scales to the box it is given (set a size with a class). The dark modules take the text colour
 * and the background is transparent, so put it on white, with room around it: that room is the
 * quiet zone a scanner needs. `title` is what a screen reader says ("QR code of the log B-012").
 *
 * `appOrigin` is the origin the printed labels must open, not necessarily where the app runs
 * (a label printed from a local or staging app would open a dead address). It must be a valid
 * address.
 */
const props = defineProps<{ appOrigin: string; logUuid: string; title: string }>();

const qr = computed(() => mushroomLogQrCode(mushroomLogQrContent(props.appOrigin, props.logUuid)));
</script>
