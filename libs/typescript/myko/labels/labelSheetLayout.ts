import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/**
 * The sheet the labels are printed on: A4, portrait, with a margin no printer cuts into. All the
 * lengths of this file are millimetres: the print stylesheet of the labels page uses them as they
 * are (`width: ${widthMm}mm`).
 */
export const LABEL_SHEET_PAGE = { widthMm: 210, heightMm: 297, marginMm: 8 } as const;

/** The two sizes of label: `standard` holds a QR code and a readable label, `compact` fits more. */
export const LABEL_SHEET_SIZE = {
  Standard: 'standard',
  Compact: 'compact',
} as const;

export type LabelSheetSize = ObjectValues<typeof LABEL_SHEET_SIZE>;

interface LabelSizeSpec {
  widthMm: number;
  heightMm: number;
  /** The space between two labels, cut lines included. */
  gapMm: number;
  /** The space between a label's edge and its content, and between its QR code and its text. */
  paddingMm: number;
}

/**
 * `standard` is the 45.7 × 25.4 mm label that sticker sheets sell 40 to an A4 sheet; `compact` is
 * 35 × 20 mm, 60 to a sheet.
 */
const LABEL_SIZE_SPECS: Record<LabelSheetSize, LabelSizeSpec> = {
  [LABEL_SHEET_SIZE.Standard]: { widthMm: 45.7, heightMm: 25.4, gapMm: 2.5, paddingMm: 2 },
  [LABEL_SHEET_SIZE.Compact]: { widthMm: 35, heightMm: 20, gapMm: 2, paddingMm: 1.5 },
};

/** Where a label sits on a sheet: the cell, and its top left corner from the sheet's top left. */
export interface LabelSheetCell {
  /** Position in reading order on the sheet, from 0. */
  index: number;
  column: number;
  row: number;
  xMm: number;
  yMm: number;
}

export interface LabelSheetLayout {
  size: LabelSheetSize;
  label: { widthMm: number; heightMm: number };
  gapMm: number;
  columns: number;
  rows: number;
  labelsPerSheet: number;
  /** The QR code is a square at the label's left, as large as the label's height allows. */
  qr: { sideMm: number; xMm: number; yMm: number };
  /** The readable label (`B-012`) takes what the QR code leaves, to its right. */
  text: { xMm: number; yMm: number; widthMm: number; heightMm: number };
  /** The cells of one sheet, in reading order. The grid is centred on the sheet. */
  cells: LabelSheetCell[];
}

export interface LabelSheetPage<T> {
  /** From 1. */
  pageNumber: number;
  labels: { item: T; cell: LabelSheetCell }[];
}

/** Float sums such as 4 × 45.7 + 3 × 2.5 must not lose a cell to 3.9999999999. */
const EPSILON = 1e-9;

function roundToHundredth(value: number): number {
  return Math.round(value * 100) / 100;
}

/** How many labels of `labelMm`, `gapMm` apart, fit in `availableMm`. */
function fitCount(availableMm: number, labelMm: number, gapMm: number): number {
  return Math.max(0, Math.floor((availableMm + gapMm) / (labelMm + gapMm) + EPSILON));
}

/**
 * The grid of one A4 sheet of labels of a size: how many fit, where each sits, and where a label's
 * QR code and text sit inside it. Pure: the labels page only draws what this says.
 */
export function labelSheetLayout(size: LabelSheetSize): LabelSheetLayout {
  const { widthMm, heightMm, gapMm, paddingMm } = LABEL_SIZE_SPECS[size];
  const page = LABEL_SHEET_PAGE;

  const columns = fitCount(page.widthMm - 2 * page.marginMm, widthMm, gapMm);
  const rows = fitCount(page.heightMm - 2 * page.marginMm, heightMm, gapMm);

  const gridWidthMm = columns * widthMm + Math.max(0, columns - 1) * gapMm;
  const gridHeightMm = rows * heightMm + Math.max(0, rows - 1) * gapMm;
  const leftMm = (page.widthMm - gridWidthMm) / 2;
  const topMm = (page.heightMm - gridHeightMm) / 2;

  const cells: LabelSheetCell[] = [];
  for (let index = 0; index < columns * rows; index++) {
    const column = index % columns;
    const row = Math.floor(index / columns);
    cells.push({
      index,
      column,
      row,
      xMm: roundToHundredth(leftMm + column * (widthMm + gapMm)),
      yMm: roundToHundredth(topMm + row * (heightMm + gapMm)),
    });
  }

  const qrSideMm = roundToHundredth(Math.min(heightMm - 2 * paddingMm, widthMm / 2));
  const textXMm = roundToHundredth(paddingMm + qrSideMm + paddingMm);

  return {
    size,
    label: { widthMm, heightMm },
    gapMm,
    columns,
    rows,
    labelsPerSheet: cells.length,
    qr: { sideMm: qrSideMm, xMm: paddingMm, yMm: roundToHundredth((heightMm - qrSideMm) / 2) },
    text: {
      xMm: textXMm,
      yMm: paddingMm,
      widthMm: roundToHundredth(widthMm - textXMm - paddingMm),
      heightMm: roundToHundredth(heightMm - 2 * paddingMm),
    },
    cells,
  };
}

/**
 * Cuts the labels into sheets, in order, each label with its cell. The last sheet may be partly
 * filled; no labels give no sheet.
 */
export function paginateLabels<T>(
  items: readonly T[],
  layout: Pick<LabelSheetLayout, 'cells'>,
): LabelSheetPage<T>[] {
  const perSheet = layout.cells.length;
  const pages: LabelSheetPage<T>[] = [];

  if (perSheet === 0) {
    return pages;
  }

  for (let start = 0; start < items.length; start += perSheet) {
    pages.push({
      pageNumber: pages.length + 1,
      labels: items.slice(start, start + perSheet).map((item, offset) => ({
        item,
        cell: layout.cells[offset]!,
      })),
    });
  }

  return pages;
}
