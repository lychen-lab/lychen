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
} as const;

export const numberFormats: I18nOptions['numberFormats'] = {
  'en-US': formats,
  'en-GB': formats,
  'fr-FR': formats,
  fr: formats,
  en: formats,
};
