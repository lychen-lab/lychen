import { type I18nOptions } from 'vue-i18n';

const formats = {
  currency: {
    style: 'currency',
    currency: 'EUR',
    currencyDisplay: 'symbol',
    useGrouping: true,
  },
  percent: {
    style: 'percent',
    useGrouping: false,
    minimumFractionDigits: 2,
  },
  millimeter: {
    style: 'unit',
    useGrouping: true,
    unit: 'millimeter',
    unitDisplay: 'short',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    roundingMode: 'halfExpand',
  },
  meter: {
    style: 'unit',
    useGrouping: true,
    unit: 'meter',
    unitDisplay: 'short',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    roundingMode: 'halfExpand',
  },
  kilogram: {
    style: 'unit',
    useGrouping: true,
    unit: 'kilogram',
    unitDisplay: 'short',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    roundingMode: 'halfExpand',
  },
  'square-meter': {
    style: 'decimal',
    useGrouping: true,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    roundingMode: 'halfExpand',
  },
} as const;

export const numberFormats: I18nOptions['numberFormats'] = {
  'en-US': formats,
  'en-GB': formats,
  'fr-FR': formats,
  fr: formats,
  en: formats,
};
