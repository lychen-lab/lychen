/** Where a land proposal stands, from its draft to its archive. */
export const LAND_PROPOSAL_STATES = ['draft', 'published', 'archived'] as const;

export type LandProposalState = (typeof LAND_PROPOSAL_STATES)[number];

/**
 * What a land proposal's workflow transitions give the front: which proposal, and where it
 * stands now. The API answers with the whole proposal; a screen that shows it fetches it.
 */
export interface LandProposalStateChange {
  ulid: string;
  /** Undefined for a state this front does not know yet. */
  state?: LandProposalState;
}
