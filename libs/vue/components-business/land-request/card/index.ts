export { default as Card } from './Card.vue';

export interface LandRequest {
  uuid: string;
  firstName: string;
  lastName: string;
  avatarUrl?: string;
  minimalSurfaceRequested: number;
  description: string;
}
