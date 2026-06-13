import {
  FEATURE_ALIAS,
  FEATURE_GROUP,
  FEATURES_LIST,
  type FeatureAlias,
  type FeatureGroup,
} from '@lychen/typescript-tera-core/constants/Feature';
import {
  useGenericApplicationsFeatures,
  type UseGenericApplicationsFeatures,
} from '@lychen/vue-composables/useGenericApplicationsFeatures';
import { TRANSLATION_KEY, messages } from './i18n';

export function useFeatures(): UseGenericApplicationsFeatures<FeatureGroup> {
  return useGenericApplicationsFeatures<FeatureAlias, FeatureGroup>(
    FEATURE_ALIAS,
    FEATURE_GROUP,
    // FEATURES_LIST is built entirely from FEATURE_ALIAS / FEATURE_GROUP values, so every entry is a
    // valid (FeatureAlias, FeatureGroup) pair. Its source declaration lacks `as const`, so TypeScript
    // widens the members to `string`; assert back to the precise element type.
    FEATURES_LIST as { alias: FeatureAlias; group: FeatureGroup }[],
    {
      messages,
      rootKey: TRANSLATION_KEY,
    },
  );
}
