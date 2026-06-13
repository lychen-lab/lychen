import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { EventBusKey } from '@vueuse/core';

export const landPostSucceededEvent: EventBusKey<
  components['schemas']['Land.jsonld-land.post_land.post.output']
> = Symbol('land-post-succeeded');
export const landDeleteSucceededEvent: EventBusKey<components['schemas']['Land.jsonld']> =
  Symbol('land-delete-succeeded');
export const landPatchSucceededEvent: EventBusKey<
  components['schemas']['Land.jsonld-land.patch_land.patch.output']
> = Symbol('land-patch-succeeded');
