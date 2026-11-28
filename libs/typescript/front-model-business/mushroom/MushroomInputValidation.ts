import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

import type { Harvest } from './Harvest';
import type { Inoculation } from './Inoculation';
import { JOURNAL_ENTRY_KIND, type JournalEntryInput } from './JournalEntry';
import type { MushroomLog } from './MushroomLog';
import type { Strain } from './Strain';
import type { Zone } from './Zone';

/**
 * Why a value of a form is refused. These are the rules of the API (see "Data model v0" in
 * `projects/myko/CLAUDE.md`), checked before the request so that a form can say so at once: the API
 * still has the last word, and a form shows its answer when it refuses anyway. A code is not a
 * message: the form translates it.
 */
export const INPUT_ISSUE_CODE = {
  Required: 'required',
  TooLong: 'too_long',
  /** A whole number of at least 1. */
  NotPositiveInteger: 'not_positive_integer',
  InFuture: 'in_future',
} as const;

export type InputIssueCode = ObjectValues<typeof INPUT_ISSUE_CODE>;

export interface InputIssue<Field extends string> {
  field: Field;
  code: InputIssueCode;
}

/** The longest name of a zone, as the API takes it. */
export const ZONE_NAME_MAX_LENGTH = 255;

function isBlank(value: string | undefined): boolean {
  return value === undefined || value.trim() === '';
}

/** A whole number of 1 or more. A number left out is not an issue here: say `Required` for it. */
function isPositiveInteger(value: number): boolean {
  return Number.isInteger(value) && value >= 1;
}

/** A day as one number to compare, in the local time zone (the month counts from 0). */
function dayNumber(date: Date): number {
  return date.getFullYear() * 10000 + date.getMonth() * 100 + date.getDate();
}

/** Whether `date`'s day, in the local time zone, is after the day of `now`. */
function isAfterToday(date: Date, now: Date): boolean {
  return dayNumber(date) > dayNumber(now);
}

/** Each optional number of a log, when given, is a whole number of 1 or more. */
function positiveIntegerIssues<Field extends string>(
  values: Record<Field, number | undefined>,
): InputIssue<Field>[] {
  return (Object.entries(values) as [Field, number | undefined][])
    .filter(([, value]) => value !== undefined && !isPositiveInteger(value))
    .map(([field]) => ({ field, code: INPUT_ISSUE_CODE.NotPositiveInteger }));
}

/** The name is required and at most `ZONE_NAME_MAX_LENGTH` characters. */
export function validateZoneInput(input: { name?: Zone['name'] }): InputIssue<'name'>[] {
  if (isBlank(input.name)) {
    return [{ field: 'name', code: INPUT_ISSUE_CODE.Required }];
  }
  if (input.name!.length > ZONE_NAME_MAX_LENGTH) {
    return [{ field: 'name', code: INPUT_ISSUE_CODE.TooLong }];
  }
  return [];
}

/**
 * The species is required (only its id is sent) and so is the name. That the species exists and
 * that the name is free among the person's strains only the API knows.
 */
export function validateStrainInput(input: {
  fungusSpecies?: Pick<NonNullable<Strain['fungusSpecies']>, 'id'>;
  name?: Strain['name'];
}): InputIssue<'fungusSpecies' | 'name'>[] {
  return [
    ...(isBlank(input.fungusSpecies?.id)
      ? [{ field: 'fungusSpecies' as const, code: INPUT_ISSUE_CODE.Required }]
      : []),
    ...(isBlank(input.name) ? [{ field: 'name' as const, code: INPUT_ISSUE_CODE.Required }] : []),
  ];
}

/**
 * The label is required, and the diameter and the length, when given, are whole numbers of 1 or
 * more. That the label is free among the person's logs only the API knows.
 */
export function validateMushroomLogInput(input: {
  label?: MushroomLog['label'];
  diameterCm?: MushroomLog['diameterCm'];
  lengthCm?: MushroomLog['lengthCm'];
}): InputIssue<'label' | 'diameterCm' | 'lengthCm'>[] {
  return [
    ...(isBlank(input.label) ? [{ field: 'label' as const, code: INPUT_ISSUE_CODE.Required }] : []),
    ...positiveIntegerIssues({ diameterCm: input.diameterCm, lengthCm: input.lengthCm }),
  ];
}

/** The day is required and not after today; the weight is a whole number of grams, 1 or more. */
export function validateHarvestInput(
  input: { harvestedAt?: Harvest['harvestedAt']; weightGrams?: Harvest['weightGrams'] },
  now: Date,
): InputIssue<'harvestedAt' | 'weightGrams'>[] {
  const issues: InputIssue<'harvestedAt' | 'weightGrams'>[] = [];

  if (input.harvestedAt === undefined) {
    issues.push({ field: 'harvestedAt', code: INPUT_ISSUE_CODE.Required });
  } else if (isAfterToday(input.harvestedAt, now)) {
    issues.push({ field: 'harvestedAt', code: INPUT_ISSUE_CODE.InFuture });
  }

  if (input.weightGrams === undefined) {
    issues.push({ field: 'weightGrams', code: INPUT_ISSUE_CODE.Required });
  } else if (!isPositiveInteger(input.weightGrams)) {
    issues.push({ field: 'weightGrams', code: INPUT_ISSUE_CODE.NotPositiveInteger });
  }

  return issues;
}

/**
 * The day is required and not after today (a day in the past is fine: logs inoculated months ago
 * are entered too); the quantity, when given, is a whole number of 1 or more.
 */
export function validateInoculationInput(
  input: { inoculatedAt?: Inoculation['inoculatedAt']; quantity?: Inoculation['quantity'] },
  now: Date,
): InputIssue<'inoculatedAt' | 'quantity'>[] {
  const issues: InputIssue<'inoculatedAt' | 'quantity'>[] = [];

  if (input.inoculatedAt === undefined) {
    issues.push({ field: 'inoculatedAt', code: INPUT_ISSUE_CODE.Required });
  } else if (isAfterToday(input.inoculatedAt, now)) {
    issues.push({ field: 'inoculatedAt', code: INPUT_ISSUE_CODE.InFuture });
  }

  return [...issues, ...positiveIntegerIssues({ quantity: input.quantity })];
}

/** The text is required for a note and an observation, optional for the other kinds. */
export function validateJournalEntryInput(input: {
  kind?: JournalEntryInput['kind'];
  text?: JournalEntryInput['text'];
}): InputIssue<'text'>[] {
  const needsText =
    input.kind === JOURNAL_ENTRY_KIND.Note || input.kind === JOURNAL_ENTRY_KIND.Observation;

  return needsText && isBlank(input.text)
    ? [{ field: 'text', code: INPUT_ISSUE_CODE.Required }]
    : [];
}
