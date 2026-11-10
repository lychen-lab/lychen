import type {
  AreaProposalJsonld,
  AreaRequestJsonld,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { LandProposal } from '@lychen/vue-components-business/land-proposal/card';
import type { LandRequest } from '@lychen/vue-components-business/land-request/card';
import areaPlaceholder from '@/assets/area-placeholder.svg';

/**
 * The API has no media yet: every proposal gets the same neutral illustration, which
 * cannot be mistaken for a photo of the place.
 */
export const AREA_PLACEHOLDER_IMAGE = areaPlaceholder;

/** The places in which a listing is public; the collections also return the user's own. */
export const PLACE_PUBLISHED = 'published';
export const PLACE_ACTIVE = 'active';

export function toLandProposalCard(proposal: AreaProposalJsonld): LandProposal {
  return {
    uuid: proposal.uuid ?? '',
    title: proposal.title ?? '',
    description: proposal.description ?? '',
    surface: proposal.surfaceToShare ?? 0,
    altitude: proposal.altitude ?? 0,
    city: proposal.city ?? undefined,
    image: AREA_PLACEHOLDER_IMAGE,
  };
}

export function toLandRequestCard(request: AreaRequestJsonld): LandRequest & { title: string } {
  return {
    uuid: request.uuid ?? '',
    title: request.title ?? '',
    firstName: request.requesterFirstName ?? '',
    lastName: request.requesterLastName ?? '',
    description: request.description ?? '',
    minimalSurfaceRequested: request.minimalSurfaceRequested ?? 0,
    city: request.city ?? '',
  };
}

export function fullName(firstName?: string | null, lastName?: string | null): string {
  return [firstName, lastName].filter(Boolean).join(' ');
}
