import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Farmer: 'farmer',
  Community: 'community',
} as const;

export const FEATURE_ALIAS = {
  CompostManagement: 'compost_management',
  Reporting: 'reporting',
  ResourceSharing: 'resource_sharing',
  ResourceUtilization: 'resource_utilization',
  ImpactTracking: 'impact_tracking',
  CompostZoneSupervision: 'compost_zone_supervision',
  WasteAnalytics: 'waste_analytics',
  PublicEngagement: 'public_engagement',
} as const;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.CompostManagement, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Reporting, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.ResourceSharing, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.ResourceUtilization, group: FEATURE_GROUP.Farmer },
  { alias: FEATURE_ALIAS.ImpactTracking, group: FEATURE_GROUP.Farmer },
  { alias: FEATURE_ALIAS.CompostZoneSupervision, group: FEATURE_GROUP.Community },
  { alias: FEATURE_ALIAS.WasteAnalytics, group: FEATURE_GROUP.Community },
  { alias: FEATURE_ALIAS.PublicEngagement, group: FEATURE_GROUP.Community },
];

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;
