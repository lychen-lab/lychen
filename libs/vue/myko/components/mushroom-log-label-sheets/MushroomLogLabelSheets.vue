<template>
  <div class="mushroom-log-label-sheets">
    <section
      v-for="page in pages"
      :key="page.pageNumber"
      class="label-sheet"
      :style="labelSheetStyle()"
      :aria-label="sheetTitle(page.pageNumber, pages.length)"
    >
      <div
        v-for="{ item, cell } in page.labels"
        :key="item.uuid"
        class="label"
        :style="labelCellStyle(layout, cell)"
      >
        <MushroomLogQrCode
          class="label-qr"
          :style="labelQrStyle(layout)"
          :app-origin="appOrigin"
          :log-uuid="item.uuid"
          :title="qrTitle(item.label)"
        />
        <span
          class="label-text"
          :style="labelTextStyle(layout, item.label)"
        >
          {{ item.label }}
        </span>
      </div>
    </section>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import {
  type LabelSheetSize,
  labelSheetLayout,
  paginateLabels,
} from '@lychen/typescript-myko/labels/labelSheetLayout';
import {
  labelCellStyle,
  labelQrStyle,
  labelSheetStyle,
  labelTextStyle,
} from '@lychen/typescript-myko/labels/labelSheetStyle';

import MushroomLogQrCode from '../mushroom-log-qr-code/MushroomLogQrCode.vue';

/**
 * The sheets of QR code labels to print, A4 one after the other: the logs, in order, laid out by
 * `labelSheetLayout` and cut into sheets by `paginateLabels`. Everything is placed in millimetres
 * from what those functions say; this component computes no geometry. On screen the sheets are a
 * preview, with the cut lines of each label; printed, only the labels remain, on pages without
 * a margin of their own (`@page`), so that a sheet of stickers lines up with the print.
 *
 * The page around it (title, size choice, print button, the app's navigation) is the labels
 * screen's: it hides what must not print. `qrTitle` and `sheetTitle` are what a screen reader says,
 * translated by the screen ("QR code of the log B-012", "Sheet 2 of 3").
 */
const props = defineProps<{
  logs: readonly { uuid: string; label: string }[];
  size: LabelSheetSize;
  /** The origin the printed labels must open: `getMushroomLogQrOrigin()` in the app. */
  appOrigin: string;
  qrTitle: (label: string) => string;
  sheetTitle: (pageNumber: number, pageCount: number) => string;
}>();

const layout = computed(() => labelSheetLayout(props.size));
const pages = computed(() => paginateLabels(props.logs, layout.value));
</script>

<style scoped>
.mushroom-log-label-sheets {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8mm;
}

/* Paper is white and ink is black, whatever the app's theme. */
.label-sheet {
  position: relative;
  box-sizing: border-box;
  flex: none;
  overflow: hidden;
  background: #fff;
  color: #000;
  box-shadow: 0 0 0 0.2mm #d4d4d8;
}

.label {
  position: absolute;
  box-sizing: border-box;
  /* The cut lines of the preview: not printed. */
  outline: 0.15mm dashed #a1a1aa;
}

.label-qr {
  position: absolute;
  display: block;
  background: #fff;
}

.label-text {
  position: absolute;
  display: flex;
  align-items: center;
  box-sizing: border-box;
  overflow: hidden;
  font-family: ui-monospace, 'SF Mono', 'Cascadia Mono', Menlo, Consolas, monospace;
  font-weight: 700;
  line-height: 1;
  /* A label too long for one line wraps instead of being cut. */
  overflow-wrap: anywhere;
}

@media print {
  .mushroom-log-label-sheets {
    display: block;
  }

  .label-sheet {
    box-shadow: none;
    break-after: page;
    page-break-after: always;
  }

  .label-sheet:last-child {
    break-after: auto;
    page-break-after: auto;
  }

  .label {
    outline: none;
  }
}
</style>

<style>
/* A4 with no margin of the browser's own: the margin of the sheet is part of its layout. */
@media print {
  @page {
    size: A4 portrait;
    margin: 0;
  }
}
</style>
