import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Farmer: 'farmer',
  Community: 'community',
} as const;

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export const FEATURE_ALIAS = {} as const;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;

export const FEATURES_LIST = [];
