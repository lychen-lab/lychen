import type {
  LandJsonld,
  LandMemberJsonldLandMemberMe,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { InjectionKey, Ref } from 'vue';

export const INJECTION_KEY_LAND: InjectionKey<Ref<LandJsonld | undefined>> = Symbol();

export const INJECTION_KEY_LAND_MEMBER: InjectionKey<
  Ref<LandMemberJsonldLandMemberMe | undefined>
> = Symbol();

export { default as LandLayout } from './LandLayout.vue';
