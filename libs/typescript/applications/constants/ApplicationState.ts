import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const APPLICATION_STATE = {
  Funding: 'funding',
  Development: 'development',
  Beta: 'beta',
  Production: 'production',
} as const;

export type ApplicationState = ObjectValues<typeof APPLICATION_STATE>;
