import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  Association: 'association',
  Producer: 'producer',
  Community: 'community',
} as const;

export const FEATURE_ALIAS = {
  FindAMAP: 'find_amap',
  Communication: 'communication',
  ServiceRequest: 'service_request',
  AssociationManagement: 'association_management',
  DataVisualization: 'data_visualization',
  FoodSecurity: 'food_security',
  ContractManagement: 'contract_management',
  DistributionPlanning: 'distribution_planning',
  SpecialSales: 'special_sales',
  CustomerCommunication: 'customer_communication',
  FoodDemocracy: 'food_democracy',
} as const;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.FindAMAP, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Communication, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.ServiceRequest, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.AssociationManagement, group: FEATURE_GROUP.Association },
  { alias: FEATURE_ALIAS.DataVisualization, group: FEATURE_GROUP.Association },
  { alias: FEATURE_ALIAS.FoodSecurity, group: FEATURE_GROUP.Association },
  { alias: FEATURE_ALIAS.ContractManagement, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.DistributionPlanning, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.SpecialSales, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.CustomerCommunication, group: FEATURE_GROUP.Producer },
  { alias: FEATURE_ALIAS.FoodDemocracy, group: FEATURE_GROUP.Community },
];

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;
