import { describe, expect, it } from 'vitest';

import { JOURNAL_ENTRY_KIND } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { JOURNAL_ENTRY_FORM_KINDS, useJournalEntryForm } from './useJournalEntryForm';

const NOW = new Date(2026, 2, 12, 8, 5, 41);

function makeForm(initialKind?: (typeof JOURNAL_ENTRY_FORM_KINDS)[number]) {
  return useJournalEntryForm({ mushroomLogId: 'log-1', initialKind, now: () => NOW });
}

describe('JOURNAL_ENTRY_FORM_KINDS', () => {
  it('offers every kind a person can write, once, and never the state change', () => {
    const everyKindButStateChange = Object.values(JOURNAL_ENTRY_KIND).filter(
      (kind) => kind !== JOURNAL_ENTRY_KIND.StateChange,
    );

    expect([...JOURNAL_ENTRY_FORM_KINDS].sort()).toEqual([...everyKindButStateChange].sort());
    expect(JOURNAL_ENTRY_FORM_KINDS).not.toContain(JOURNAL_ENTRY_KIND.StateChange);
  });
});

describe('useJournalEntryForm', () => {
  it('starts with no kind, no text and now to the minute, and shows no issue yet', () => {
    const form = makeForm();

    expect(form.kind.value).toBeUndefined();
    expect(form.text.value).toBe('');
    expect(form.occurredAt.value).toBe('2026-03-12T08:05');
    expect(form.issue('kind')).toBeUndefined();
    expect(form.issue('text')).toBeUndefined();
  });

  it('asks for a kind before anything else', () => {
    const form = makeForm();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('kind')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('gives a soaking in two moves: a kind, then submit', () => {
    const form = makeForm();
    form.kind.value = JOURNAL_ENTRY_KIND.Soaking;

    expect(form.submit()).toEqual({
      mushroomLog: { id: 'log-1' },
      kind: JOURNAL_ENTRY_KIND.Soaking,
      occurredAt: new Date(2026, 2, 12, 8, 5),
    });
  });

  it('starts on the initial kind, and goes back to it on reset', () => {
    const form = makeForm(JOURNAL_ENTRY_KIND.Watering);
    expect(form.kind.value).toBe(JOURNAL_ENTRY_KIND.Watering);

    form.kind.value = JOURNAL_ENTRY_KIND.Note;
    form.reset();
    expect(form.kind.value).toBe(JOURNAL_ENTRY_KIND.Watering);
  });

  it('requires a text for a note and an observation only', () => {
    const form = makeForm();

    form.kind.value = JOURNAL_ENTRY_KIND.Note;
    expect(form.isTextRequired.value).toBe(true);
    expect(form.submit()).toBeUndefined();
    expect(form.issue('text')).toBe(INPUT_ISSUE_CODE.Required);

    form.kind.value = JOURNAL_ENTRY_KIND.Observation;
    expect(form.isTextRequired.value).toBe(true);

    form.kind.value = JOURNAL_ENTRY_KIND.Treatment;
    expect(form.isTextRequired.value).toBe(false);
    expect(form.issue('text')).toBeUndefined();
    expect(form.submit()).toBeDefined();
  });

  it('does not call a text required while no kind is chosen', () => {
    expect(makeForm().isTextRequired.value).toBe(false);
  });

  it('refuses a note of blanks, and sends a text trimmed', () => {
    const form = makeForm();
    form.kind.value = JOURNAL_ENTRY_KIND.Note;
    form.text.value = '   ';
    expect(form.submit()).toBeUndefined();

    form.text.value = '  mycélium blanc visible  ';
    expect(form.submit()?.text).toBe('mycélium blanc visible');
  });

  it('leaves the text out when blank for a kind that does not need it', () => {
    const form = makeForm(JOURNAL_ENTRY_KIND.Moving);
    form.text.value = ' ';

    expect(form.submit()).not.toHaveProperty('text');
  });

  it('leaves the moment out when the field is empty, for the API to use the current time', () => {
    const form = makeForm(JOURNAL_ENTRY_KIND.Soaking);
    form.occurredAt.value = '';

    expect(form.submit()).not.toHaveProperty('occurredAt');
  });

  it('sends the moment that was picked, in local time', () => {
    const form = makeForm(JOURNAL_ENTRY_KIND.Soaking);
    form.occurredAt.value = '2026-03-10T19:30';

    expect(form.submit()?.occurredAt).toEqual(new Date(2026, 2, 10, 19, 30));
  });

  it('follows a log id that changes', () => {
    let id = 'log-1';
    const form = useJournalEntryForm({
      mushroomLogId: () => id,
      initialKind: JOURNAL_ENTRY_KIND.Soaking,
      now: () => NOW,
    });
    id = 'log-2';

    expect(form.submit()?.mushroomLog).toEqual({ id: 'log-2' });
  });

  it('follows what is typed once a submission has been tried', () => {
    const form = makeForm();
    form.kind.value = JOURNAL_ENTRY_KIND.Note;
    form.submit();
    expect(form.issue('text')).toBe(INPUT_ISSUE_CODE.Required);

    form.text.value = 'ok';
    expect(form.issue('text')).toBeUndefined();
  });

  it('resets every field and hides the issues', () => {
    const form = makeForm();
    form.kind.value = JOURNAL_ENTRY_KIND.Note;
    form.text.value = 'x';
    form.occurredAt.value = '2026-01-01T00:00';
    form.submit();
    form.text.value = '';
    form.submit();

    form.reset();

    expect(form.kind.value).toBeUndefined();
    expect(form.text.value).toBe('');
    expect(form.occurredAt.value).toBe('2026-03-12T08:05');
    expect(form.issue('kind')).toBeUndefined();
    expect(form.issue('text')).toBeUndefined();
  });
});
