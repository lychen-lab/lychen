import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Producer: 'producer',
  Community: 'community',
} as const;

export const FEATURE_ALIAS = {
  StoreDiscovery: 'store_discovery',
  ProductReservation: 'product_reservation',
  CommunityEngagement: 'community_engagement',
  StoreManagement: 'store_management',
  Communication: 'communication',
  CertificationManagement: 'certification_management',
  LocalEconomySupport: 'local_economy_support',
  DataVisualization: 'data_visualization',
} as const;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.StoreDiscovery, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.ProductReservation, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.CommunityEngagement, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.StoreManagement, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.Communication, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.CertificationManagement, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.LocalEconomySupport, group: FEATURE_GROUP.Community },
  { alias: FEATURE_ALIAS.DataVisualization, group: FEATURE_GROUP.Community },
];

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;
