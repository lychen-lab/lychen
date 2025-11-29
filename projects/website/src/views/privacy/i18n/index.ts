import { buildConfig } from '@lychen/vue-i18n/composables/useI18nExtended';

export const CONFIG = buildConfig(import.meta.glob('./*.json', { eager: true }), 'view_privacy');
