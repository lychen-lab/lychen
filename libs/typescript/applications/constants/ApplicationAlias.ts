import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

import { APP_ALIAS as TERA_APP_ALIAS } from '@lychen/typescript-tera-core/constants/App';
import { APP_ALIAS as MYKO_APP_ALIAS } from '@lychen/typescript-myko/constants/App';
import { APP_ALIAS as KIRO_APP_ALIAS } from '@lychen/typescript-kiro/constants/App';
import { APP_ALIAS as MELI_APP_ALIAS } from '@lychen/typescript-meli/constants/App';
import { APP_ALIAS as HUMU_APP_ALIAS } from '@lychen/typescript-humu/constants/App';
import { APP_ALIAS as KOLO_APP_ALIAS } from '@lychen/typescript-kolo/constants/App';
import { APP_ALIAS as VARA_APP_ALIAS } from '@lychen/typescript-vara/constants/App';
import { APP_ALIAS as NOVI_APP_ALIAS } from '@lychen/typescript-novi/constants/App';
import { APP_ALIAS as ROBUST_APP_ALIAS } from '@lychen/typescript-robust/constants/App';
import { APP_ALIAS as ESPACE_APP_ALIAS } from '@lychen/typescript-espace/constants/App';

export const APPLICATION_ALIAS = {
  Espace: ESPACE_APP_ALIAS,
  Kiro: KIRO_APP_ALIAS,
  Humu: HUMU_APP_ALIAS,
  Novi: NOVI_APP_ALIAS,
  Vara: VARA_APP_ALIAS,
  Meli: MELI_APP_ALIAS,
  Kolo: KOLO_APP_ALIAS,
  Tera: TERA_APP_ALIAS,
  Myko: MYKO_APP_ALIAS,
  Robust: ROBUST_APP_ALIAS,
} as const;

export type ApplicationAlias = ObjectValues<typeof APPLICATION_ALIAS>;
