import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Beekeeper: 'beekeeper',
  Professional: 'professional',
} as const;

export const FEATURE_ALIAS = {
  ApiaryManagement: 'apiary_management',
  TaskManagement: 'task_management',
  InterventionsLogging: 'interventions_logging',
  DataAnalysis: 'data_analysis',
  VitalityTracking: 'vitality_tracking',
  Certifications: 'certifications',
  Marketing: 'marketing',
} as const;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.ApiaryManagement, group: FEATURE_GROUP.Beekeeper },
  { alias: FEATURE_ALIAS.TaskManagement, group: FEATURE_GROUP.Beekeeper },
  { alias: FEATURE_ALIAS.InterventionsLogging, group: FEATURE_GROUP.Beekeeper },
  { alias: FEATURE_ALIAS.DataAnalysis, group: FEATURE_GROUP.Beekeeper },
  { alias: FEATURE_ALIAS.VitalityTracking, group: FEATURE_GROUP.Beekeeper },
  { alias: FEATURE_ALIAS.Certifications, group: FEATURE_GROUP.Professional },
  { alias: FEATURE_ALIAS.Marketing, group: FEATURE_GROUP.Professional },
];

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;
