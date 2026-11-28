import { describe, expect, it } from 'vitest';

import {
  LABEL_SHEET_PAGE,
  LABEL_SHEET_SIZE,
  type LabelSheetSize,
  labelSheetLayout,
  paginateLabels,
} from './labelSheetLayout';

const SIZES = Object.values(LABEL_SHEET_SIZE) as LabelSheetSize[];

describe('labelSheetLayout', () => {
  it('puts 40 standard labels (4 × 10) on an A4 sheet', () => {
    const layout = labelSheetLayout(LABEL_SHEET_SIZE.Standard);

    expect(layout.columns).toBe(4);
    expect(layout.rows).toBe(10);
    expect(layout.labelsPerSheet).toBe(40);
    expect(layout.cells).toHaveLength(40);
    expect(layout.label).toEqual({ widthMm: 45.7, heightMm: 25.4 });
  });

  it('puts 60 compact labels (5 × 12) on an A4 sheet', () => {
    const layout = labelSheetLayout(LABEL_SHEET_SIZE.Compact);

    expect(layout.columns).toBe(5);
    expect(layout.rows).toBe(12);
    expect(layout.labelsPerSheet).toBe(60);
  });

  it('centres the grid on the sheet', () => {
    const layout = labelSheetLayout(LABEL_SHEET_SIZE.Standard);

    // 4 × 45.7 + 3 × 2.5 = 190.3 mm wide, 10 × 25.4 + 9 × 2.5 = 276.5 mm high.
    expect(layout.cells[0]).toEqual({ index: 0, column: 0, row: 0, xMm: 9.85, yMm: 10.25 });
    expect(layout.cells[39]).toEqual({ index: 39, column: 3, row: 9, xMm: 154.45, yMm: 261.35 });
  });

  it('numbers the cells in reading order, left to right then top to bottom', () => {
    const { cells } = labelSheetLayout(LABEL_SHEET_SIZE.Standard);

    expect(cells[3]).toMatchObject({ column: 3, row: 0 });
    expect(cells[4]).toMatchObject({ column: 0, row: 1 });
    expect(cells.map((cell) => cell.index)).toEqual(cells.map((_, index) => index));
  });

  it.each(SIZES)('keeps every %s label inside the margins of the sheet', (size) => {
    const layout = labelSheetLayout(size);
    const { marginMm, widthMm, heightMm } = LABEL_SHEET_PAGE;

    for (const cell of layout.cells) {
      expect(cell.xMm).toBeGreaterThanOrEqual(marginMm);
      expect(cell.yMm).toBeGreaterThanOrEqual(marginMm);
      expect(cell.xMm + layout.label.widthMm).toBeLessThanOrEqual(widthMm - marginMm + 0.01);
      expect(cell.yMm + layout.label.heightMm).toBeLessThanOrEqual(heightMm - marginMm + 0.01);
    }
  });

  it.each(SIZES)('leaves the gap between two %s labels, and no overlap', (size) => {
    const layout = labelSheetLayout(size);
    const [first, second] = layout.cells;
    const below = layout.cells[layout.columns]!;

    expect(second!.xMm - first!.xMm - layout.label.widthMm).toBeCloseTo(layout.gapMm, 5);
    expect(below.yMm - first!.yMm - layout.label.heightMm).toBeCloseTo(layout.gapMm, 5);
  });

  it.each(SIZES)('fits the QR code and the text of a %s label inside it', (size) => {
    const { label, qr, text } = labelSheetLayout(size);

    expect(qr.sideMm).toBeGreaterThan(0);
    expect(qr.xMm + qr.sideMm).toBeLessThanOrEqual(text.xMm);
    expect(qr.yMm).toBeGreaterThanOrEqual(0);
    expect(qr.yMm + qr.sideMm).toBeLessThanOrEqual(label.heightMm);
    expect(text.widthMm).toBeGreaterThan(0);
    expect(text.xMm + text.widthMm).toBeLessThanOrEqual(label.widthMm);
    expect(text.yMm + text.heightMm).toBeLessThanOrEqual(label.heightMm);
  });

  it('gives a QR code that is large enough to scan outdoors', () => {
    // 15 mm is the usual floor for a QR code of this density scanned from a phone at arm's length.
    for (const size of SIZES) {
      expect(labelSheetLayout(size).qr.sideMm).toBeGreaterThanOrEqual(15);
    }
  });
});

describe('paginateLabels', () => {
  const standard = labelSheetLayout(LABEL_SHEET_SIZE.Standard);
  function labels(count: number): string[] {
    return Array.from({ length: count }, (_, i) => `B-${i + 1}`);
  }

  it('gives no sheet when there is no label', () => {
    expect(paginateLabels([], standard)).toEqual([]);
  });

  it('fills one sheet exactly with 40 standard labels', () => {
    const pages = paginateLabels(labels(40), standard);

    expect(pages).toHaveLength(1);
    expect(pages[0]!.labels).toHaveLength(40);
  });

  it('starts a second sheet with the 41st label, in its first cell', () => {
    const pages = paginateLabels(labels(41), standard);

    expect(pages.map((page) => page.labels.length)).toEqual([40, 1]);
    expect(pages[1]!.pageNumber).toBe(2);
    expect(pages[1]!.labels[0]).toEqual({ item: 'B-41', cell: standard.cells[0] });
  });

  it('keeps the order and puts each label in the cell of its place', () => {
    const [page] = paginateLabels(labels(6), standard);

    expect(page!.labels.map((entry) => entry.item)).toEqual([
      'B-1',
      'B-2',
      'B-3',
      'B-4',
      'B-5',
      'B-6',
    ]);
    expect(page!.labels[5]!.cell).toMatchObject({ column: 1, row: 1 });
  });

  it('numbers the sheets from 1 and fills all but the last', () => {
    const pages = paginateLabels(labels(100), standard);

    expect(pages.map((page) => page.pageNumber)).toEqual([1, 2, 3]);
    expect(pages.map((page) => page.labels.length)).toEqual([40, 40, 20]);
  });

  it('works for any item, not only labels as text', () => {
    const logs = [{ uuid: 'a' }, { uuid: 'b' }];
    const [page] = paginateLabels(logs, labelSheetLayout(LABEL_SHEET_SIZE.Compact));

    expect(page!.labels[1]!.item).toBe(logs[1]);
  });
});
