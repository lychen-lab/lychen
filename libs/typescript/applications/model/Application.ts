import type { ApplicationAlias } from '../constants/ApplicationAlias';
import type { ApplicationState } from '../constants/ApplicationState';

export interface Application {
  title: string;
  description: string;
  state: ApplicationState;
  alias: ApplicationAlias;
}
