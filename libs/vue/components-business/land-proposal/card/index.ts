export { default as Card } from './Card.vue';

export interface LandProposal {
  uuid: string;
  name: string;
  description: string;
  surface: number;
  altitude: number;
}
