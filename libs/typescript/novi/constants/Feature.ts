import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Scientist: 'scientist',
} as const;

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export const FEATURE_ALIAS = {
  DataExploration: 'data_exploration',
  ScientificProjects: 'scientific_projects',
  Education: 'education',
  Collaboration: 'collaboration',
  DataAccess: 'data_access',
  KnowledgeSharing: 'knowledge_sharing',
  DataManagement: 'data_management',
  AdvancedAnalysis: 'advanced_analysis',
} as const;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.DataExploration, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.ScientificProjects, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Education, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Collaboration, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.DataAccess, group: FEATURE_GROUP.Scientist },
  { alias: FEATURE_ALIAS.KnowledgeSharing, group: FEATURE_GROUP.Scientist },
  { alias: FEATURE_ALIAS.DataManagement, group: FEATURE_GROUP.Scientist },
  { alias: FEATURE_ALIAS.AdvancedAnalysis, group: FEATURE_GROUP.Scientist },
];
