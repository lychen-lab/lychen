import type { AreaProposal } from '@lychen/typescript-front-model-business/area/AreaProposal';
import type { AreaRequest } from '@lychen/typescript-front-model-business/area/AreaRequest';
import type { PersonName } from '@lychen/typescript-front-model-business/person/PersonName';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import type { LandProposal } from '@lychen/vue-components-business/land-proposal/card';
import type { LandRequest } from '@lychen/vue-components-business/land-request/card';
import areaPlaceholder from '@/assets/area-placeholder.svg';

/**
 * The API has no media yet: every proposal gets the same neutral illustration, which
 * cannot be mistaken for a photo of the place.
 */
export const AREA_PLACEHOLDER_IMAGE = areaPlaceholder;

/** The props of the proposal card, from an area proposal of the front's model. */
export function toLandProposalCard(
  proposal: WithRequired<AreaProposal, 'id' | 'title' | 'description'>,
): LandProposal {
  return {
    uuid: proposal.id,
    title: proposal.title,
    description: proposal.description,
    surface: proposal.surfaceToShare ?? 0,
    altitude: proposal.altitude ?? 0,
    city: proposal.city,
    image: AREA_PLACEHOLDER_IMAGE,
  };
}

/** The props of the request card, from an area request of the front's model. */
export function toLandRequestCard(
  request: WithRequired<AreaRequest, 'id' | 'title' | 'description'>,
): LandRequest & { title: string } {
  return {
    uuid: request.id,
    title: request.title,
    firstName: request.requester?.firstName ?? '',
    lastName: request.requester?.lastName ?? '',
    description: request.description,
    minimalSurfaceRequested: request.minimalSurfaceRequested ?? 0,
    city: request.city ?? '',
  };
}

export function fullName(person: PersonName | undefined): string {
  return [person?.firstName, person?.lastName].filter(Boolean).join(' ');
}
