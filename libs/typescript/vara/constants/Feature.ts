import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Association: 'association',
} as const;

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export const FEATURE_ALIAS = {
  ParticipatorySurveys: 'participatory_surveys',
  DataCollection: 'data_collection',
  TerritoryInformation: 'territory_information',
  StatisticsVisualization: 'statistics_visualization',
  DataArchiving: 'data_archiving',
  KnowledgeSharing: 'knowledge_sharing',
  PublicAwareness: 'public_awareness',
} as const;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.ParticipatorySurveys, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.DataCollection, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.TerritoryInformation, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.StatisticsVisualization, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.DataArchiving, group: FEATURE_GROUP.Association },
  { alias: FEATURE_ALIAS.KnowledgeSharing, group: FEATURE_GROUP.Association },
  { alias: FEATURE_ALIAS.PublicAwareness, group: FEATURE_GROUP.Association },
];
