import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const FEATURE_GROUP = {
  Citizen: 'citizen',
  MarketGardener: 'market_gardener',
  Community: 'community',
} as const;

export type FeatureGroup = ObjectValues<typeof FEATURE_GROUP>;

export const FEATURE_ALIAS = {
  CultureSpace: 'culture_space',
  Planning: 'planning',
  SeedLibrary: 'seed_library',
  Logging: 'logging',
  Data: 'data',
  Resources: 'resources',
  Certifications: 'certifications',
  Traceability: 'traceability',
  Marketing: 'marketing',
  CultureSpacesManagement: 'culture_spaces_management',
  FoodSovereignty: 'food_sovereignty',
  FoodEducation: 'food_education',
  ResourcesManagement: 'resources_management',
} as const;

export type FeatureAlias = ObjectValues<typeof FEATURE_ALIAS>;

export const FEATURES_LIST = [
  { alias: FEATURE_ALIAS.CultureSpace, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Planning, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.SeedLibrary, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Logging, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Data, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Resources, group: FEATURE_GROUP.Citizen },
  { alias: FEATURE_ALIAS.Certifications, group: FEATURE_GROUP.MarketGardener },
  { alias: FEATURE_ALIAS.Traceability, group: FEATURE_GROUP.MarketGardener },
  { alias: FEATURE_ALIAS.Marketing, group: FEATURE_GROUP.MarketGardener },
  {
    alias: FEATURE_ALIAS.CultureSpacesManagement,
    group: FEATURE_GROUP.Community,
  },
  { alias: FEATURE_ALIAS.FoodSovereignty, group: FEATURE_GROUP.Community },
  { alias: FEATURE_ALIAS.FoodEducation, group: FEATURE_GROUP.Community },
  { alias: FEATURE_ALIAS.ResourcesManagement, group: FEATURE_GROUP.Community },
];
