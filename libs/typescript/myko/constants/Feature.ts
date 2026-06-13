import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Mycologist: 'mycologist',
  Professional: 'professional',
} as const;

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export const FEATURE_ALIAS = {
  CultureManagement: 'culture_management',
  Planning: 'planning',
  InterventionsLogging: 'interventions_logging',
  DataAnalysis: 'data_analysis',
  Certifications: 'certifications',
  Marketing: 'marketing',
} as const;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.CultureManagement, group: FEATURE_GROUP.Mycologist },
  { alias: FEATURE_ALIAS.Planning, group: FEATURE_GROUP.Mycologist },
  { alias: FEATURE_ALIAS.InterventionsLogging, group: FEATURE_GROUP.Mycologist },
  { alias: FEATURE_ALIAS.DataAnalysis, group: FEATURE_GROUP.Mycologist },
  { alias: FEATURE_ALIAS.Certifications, group: FEATURE_GROUP.Professional },
  { alias: FEATURE_ALIAS.Marketing, group: FEATURE_GROUP.Professional },
];
