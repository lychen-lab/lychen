import { buildConfig } from '@lychen/vue-i18n/composables/useI18nExtended';
import { APP_ALIAS } from '@lychen/typescript-espace/constants/App';

export const CONFIG = buildConfig(import.meta.glob('./*.json', { eager: true }), APP_ALIAS);
