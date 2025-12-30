export { default as Card } from './Card.vue';

export interface LandProposal {
  uuid: string;
  title: string;
  description: string;
  surface: number;
  altitude: number;
  city: string;
  image: string;
}
