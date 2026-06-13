import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { InjectionKey, Ref } from 'vue';

export const INJECTION_KEY_LAND: InjectionKey<
  Ref<components['schemas']['Land.jsonld'] | undefined>
> = Symbol();

export const INJECTION_KEY_LAND_MEMBER: InjectionKey<
  Ref<components['schemas']['LandMember.jsonld-land_member.me'] | undefined>
> = Symbol();

export { default as LandLayout } from './LandLayout.vue';
