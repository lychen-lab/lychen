import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** Where a land proposal stands, from its draft to its archive. */
export const LAND_PROPOSAL_STATE = {
  Draft: 'draft',
  Published: 'published',
  Archived: 'archived',
} as const;

export type LandProposalState = ObjectValues<typeof LAND_PROPOSAL_STATE>;

/**
 * A land its owner offers to share. Every field is optional: an endpoint says which ones it
 * gives, a component which ones it needs (`WithRequired`).
 */
export interface LandProposal {
  id?: string;
  /** Undefined for a state this front does not know yet. */
  state?: LandProposalState;
}
