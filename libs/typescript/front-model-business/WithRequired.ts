/**
 * A concept with these fields guaranteed. Every field of a concept is optional, as no
 * endpoint gives them all: an endpoint's `mapToModel` says with this type which fields it
 * always gives, and a component which ones it needs. TypeScript then checks, where a page
 * passes an endpoint's data to a component, that the one covers the other.
 *
 *   export type Model = WithRequired<AreaProposal, 'id' | 'title'>; // in mapToModel.ts
 *   defineProps<{ proposal: WithRequired<AreaProposal, 'title'> }>(); // in a component
 */
export type WithRequired<T, K extends keyof T> = T & Required<Pick<T, K>>;
