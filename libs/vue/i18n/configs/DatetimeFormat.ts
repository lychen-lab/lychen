import { type I18nOptions } from 'vue-i18n';

const formats = {
  long: {
    day: '2-digit',
    hour: 'numeric',
    minute: 'numeric',
    month: 'short',
    year: 'numeric',
  },
  'month-year': {
    month: 'short',
    year: 'numeric',
  },
  numeric: {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  },
  short: {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  },
  time: {
    hour: 'numeric',
    minute: 'numeric',
  },
} as const;

export const datetimeFormats: I18nOptions['datetimeFormats'] = {
  'en-US': formats,
  'en-GB': formats,
  'fr-FR': formats,
  fr: formats,
  en: formats,
};
