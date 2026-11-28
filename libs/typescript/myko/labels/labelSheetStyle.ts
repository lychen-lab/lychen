import { LABEL_SHEET_PAGE, type LabelSheetCell, type LabelSheetLayout } from './labelSheetLayout';

/**
 * The CSS of a sheet of labels, in millimetres, from what `labelSheetLayout` says: the component
 * that draws the sheet binds these values as they are and computes nothing, as the print
 * stylesheet of the labels page must (projects/myko/CLAUDE.md, "App conventions").
 */

/** A length in millimetres as CSS: `45.7mm`, `0mm`, never `45.700000000001mm`. */
export function mm(valueMm: number): string {
  return `${Number(valueMm.toFixed(2))}mm`;
}

/**
 * How wide a character of the monospace font of the labels is, in em. Real fonts run from 0.6 to
 * 0.62: taking the wider keeps a text inside its box whichever one the printer's browser picks.
 */
export const LABEL_TEXT_CHAR_WIDTH_EM = 0.62;

/** The text of a label is never taller than this share of its box: a short label stays a label. */
export const LABEL_TEXT_MAX_HEIGHT_RATIO = 0.5;

/** Below this a printed text is not legible (about 4 pt): a longer text wraps instead of shrinking. */
export const LABEL_TEXT_MIN_FONT_SIZE_MM = 1.5;

/**
 * The font size of the readable label (`B-012`), in millimetres: as large as lets it stay on one
 * line in the box the layout gives it, without being taller than the box allows and never below
 * the legible minimum.
 */
export function labelTextFontSizeMm(
  text: string,
  box: { widthMm: number; heightMm: number },
): number {
  const characters = Math.max(1, [...text].length);
  const fittingTheWidth = box.widthMm / (characters * LABEL_TEXT_CHAR_WIDTH_EM);
  const fittingTheHeight = box.heightMm * LABEL_TEXT_MAX_HEIGHT_RATIO;

  return Math.max(LABEL_TEXT_MIN_FONT_SIZE_MM, Math.min(fittingTheWidth, fittingTheHeight));
}

/** One sheet: A4, the box every label is placed in. */
export function labelSheetStyle(): { width: string; height: string } {
  return { width: mm(LABEL_SHEET_PAGE.widthMm), height: mm(LABEL_SHEET_PAGE.heightMm) };
}

/** A label: its cell on the sheet. */
export function labelCellStyle(
  layout: Pick<LabelSheetLayout, 'label'>,
  cell: Pick<LabelSheetCell, 'xMm' | 'yMm'>,
): { left: string; top: string; width: string; height: string } {
  return {
    left: mm(cell.xMm),
    top: mm(cell.yMm),
    width: mm(layout.label.widthMm),
    height: mm(layout.label.heightMm),
  };
}

/** The QR code of a label, from the label's top left corner. */
export function labelQrStyle(layout: Pick<LabelSheetLayout, 'qr'>): {
  left: string;
  top: string;
  width: string;
  height: string;
} {
  return {
    left: mm(layout.qr.xMm),
    top: mm(layout.qr.yMm),
    width: mm(layout.qr.sideMm),
    height: mm(layout.qr.sideMm),
  };
}

/** The readable label, from the label's top left corner. */
export function labelTextStyle(
  layout: Pick<LabelSheetLayout, 'text'>,
  text: string,
): { left: string; top: string; width: string; height: string; fontSize: string } {
  return {
    left: mm(layout.text.xMm),
    top: mm(layout.text.yMm),
    width: mm(layout.text.widthMm),
    height: mm(layout.text.heightMm),
    fontSize: mm(labelTextFontSizeMm(text, layout.text)),
  };
}
