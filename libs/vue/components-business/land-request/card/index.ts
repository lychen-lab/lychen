export { default as Card } from './Card.vue';

export interface LandRequest {
  uuid: string;
  firstName: string;
  lastName: string;
  minimalSurfaceRequested: number;
  description: string;
}
