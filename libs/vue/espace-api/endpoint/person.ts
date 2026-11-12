import { optional } from '@lychen/typescript-api-mapping/mapping';
import type { PersonName } from '@lychen/typescript-front-model-business/person/PersonName';

/** A first and a last name, or undefined when the API gives neither. */
export function toPersonName(
  firstName: string | null | undefined,
  lastName: string | null | undefined,
): PersonName | undefined {
  if (!firstName && !lastName) {
    return undefined;
  }
  return { firstName: optional(firstName), lastName: optional(lastName) };
}
