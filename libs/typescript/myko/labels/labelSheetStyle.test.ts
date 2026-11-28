import { describe, expect, it } from 'vitest';

import { LABEL_SHEET_SIZE, labelSheetLayout, type LabelSheetSize } from './labelSheetLayout';
import {
  labelCellStyle,
  labelQrStyle,
  labelSheetStyle,
  labelTextFontSizeMm,
  labelTextStyle,
  LABEL_TEXT_MIN_FONT_SIZE_MM,
  mm,
} from './labelSheetStyle';

const SIZES = Object.values(LABEL_SHEET_SIZE) as LabelSheetSize[];

describe('mm', () => {
  it('writes a length in millimetres, without float noise or trailing zeros', () => {
    expect(mm(45.7)).toBe('45.7mm');
    expect(mm(8)).toBe('8mm');
    expect(mm(0)).toBe('0mm');
    expect(mm(0.1 + 0.2)).toBe('0.3mm');
    expect(mm(25.400000000000002)).toBe('25.4mm');
    expect(mm(2.345)).toBe('2.35mm');
  });
});

describe('labelSheetStyle', () => {
  it('is an A4 sheet', () => {
    expect(labelSheetStyle()).toEqual({ width: '210mm', height: '297mm' });
  });
});

describe('labelCellStyle, labelQrStyle and labelTextStyle', () => {
  const layout = labelSheetLayout(LABEL_SHEET_SIZE.Standard);

  it('places a label by its cell, at the size of the label', () => {
    const cell = layout.cells[5]!;

    expect(labelCellStyle(layout, cell)).toEqual({
      left: `${cell.xMm}mm`,
      top: `${cell.yMm}mm`,
      width: '45.7mm',
      height: '25.4mm',
    });
  });

  it('draws the QR code as a square', () => {
    const style = labelQrStyle(layout);

    expect(style.width).toBe(style.height);
    expect(style.width).toBe(`${layout.qr.sideMm}mm`);
    expect(style.left).toBe(`${layout.qr.xMm}mm`);
    expect(style.top).toBe(`${layout.qr.yMm}mm`);
  });

  it('boxes the text where the layout says, with a font size that fits', () => {
    const style = labelTextStyle(layout, 'B-012');

    expect(style.left).toBe(`${layout.text.xMm}mm`);
    expect(style.top).toBe(`${layout.text.yMm}mm`);
    expect(style.width).toBe(`${layout.text.widthMm}mm`);
    expect(style.height).toBe(`${layout.text.heightMm}mm`);
    expect(style.fontSize).toBe(mm(labelTextFontSizeMm('B-012', layout.text)));
  });
});

describe('labelTextFontSizeMm', () => {
  const box = { widthMm: 18.3, heightMm: 21.4 };

  it('fills the width of the box with a short label', () => {
    expect(labelTextFontSizeMm('B-012', box)).toBeCloseTo(18.3 / (5 * 0.62), 5);
  });

  it('is smaller for a longer label', () => {
    expect(labelTextFontSizeMm('B-0123456', box)).toBeLessThan(labelTextFontSizeMm('B-012', box));
  });

  it('is never taller than half of the box, however short the label', () => {
    expect(labelTextFontSizeMm('A', box)).toBe(21.4 / 2);
  });

  it('never goes below the legible minimum, so that a very long label wraps instead', () => {
    expect(labelTextFontSizeMm('Zone-Nord-Chene-012345678', box)).toBe(LABEL_TEXT_MIN_FONT_SIZE_MM);
  });

  it('counts a character with an accent or an emoji once', () => {
    expect(labelTextFontSizeMm('Chêne', box)).toBe(labelTextFontSizeMm('Chene', box));
    expect(labelTextFontSizeMm('🍄🍄', box)).toBe(labelTextFontSizeMm('ab', box));
  });

  it('copes with an empty text', () => {
    expect(labelTextFontSizeMm('', box)).toBe(21.4 / 2);
  });

  it.each(SIZES)('keeps an ordinary label legible on a %s label', (size) => {
    const { text } = labelSheetLayout(size);

    expect(labelTextFontSizeMm('B-012', text)).toBeGreaterThan(LABEL_TEXT_MIN_FONT_SIZE_MM);
    expect(labelTextFontSizeMm('B-0123', text)).toBeGreaterThan(LABEL_TEXT_MIN_FONT_SIZE_MM);
  });
});
