import {
  APPLICATION_ALIAS,
  type ApplicationAlias,
} from '@lychen/typescript-applications/constants/ApplicationAlias';
import {
  APPLICATION_STATE,
  type ApplicationState,
} from '@lychen/typescript-applications/constants/ApplicationState';

export const APPLICATIONS: Record<ApplicationAlias, { state: ApplicationState }> = {
  [APPLICATION_ALIAS.Tera]: { state: APPLICATION_STATE.Development },
  [APPLICATION_ALIAS.Myko]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Kiro]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Meli]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Humu]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Kolo]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Vara]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Novi]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Robust]: { state: APPLICATION_STATE.Funding },
  [APPLICATION_ALIAS.Espace]: { state: APPLICATION_STATE.Development },
};
