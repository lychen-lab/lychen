/** A locale file's messages: strings, in objects nested as deep as the file wants. */
export interface LocaleMessages {
  [key: string]: string | LocaleMessages;
}

/** The two languages every string of Myko is written in. */
export const MYKO_LOCALES = ['fr-FR', 'en-US'] as const;

/** `{ a: { b: 'x' } }` as `{ 'a.b': 'x' }`. */
export function flattenLocaleMessages(
  messages: LocaleMessages,
  prefix = '',
): Record<string, string> {
  const flat: Record<string, string> = {};

  for (const [key, value] of Object.entries(messages)) {
    const path = prefix ? `${prefix}.${key}` : key;

    if (typeof value === 'object' && value !== null) {
      Object.assign(flat, flattenLocaleMessages(value, path));
    } else {
      flat[path] = String(value);
    }
  }

  return flat;
}

/** The names of the `{name}` (or `{0}`) placeholders of a message, each once, sorted. */
export function messagePlaceholders(message: string): string[] {
  const names = [...message.matchAll(/\{\s*([\w.]+)\s*\}/g)].map((match) => match[1] as string);

  return [...new Set(names)].sort();
}

/**
 * What differs between two languages of the same locale folder, one sentence per problem: a key
 * only one language has, a message left empty, a `{placeholder}` one language lost or renamed
 * (the sentence would then show the raw `{name}`). Empty when they say the same things.
 */
export function compareLocales(
  first: LocaleMessages,
  second: LocaleMessages,
  [firstName, secondName]: readonly [string, string] = MYKO_LOCALES,
): string[] {
  const a = flattenLocaleMessages(first);
  const b = flattenLocaleMessages(second);
  const problems: string[] = [];

  for (const key of Object.keys(a).sort()) {
    if (!(key in b)) {
      problems.push(`${key}: missing in ${secondName}`);
    }
  }

  for (const key of Object.keys(b).sort()) {
    if (!(key in a)) {
      problems.push(`${key}: missing in ${firstName}`);
    }
  }

  for (const [name, messages] of [
    [firstName, a],
    [secondName, b],
  ] as const) {
    for (const key of Object.keys(messages).sort()) {
      if ((messages[key] ?? '').trim() === '') {
        problems.push(`${key}: empty in ${name}`);
      }
    }
  }

  for (const key of Object.keys(a).sort()) {
    const left = messagePlaceholders(a[key] ?? '').join(', ');
    const right = messagePlaceholders(b[key] ?? '').join(', ');

    if (key in b && left !== right) {
      problems.push(
        `${key}: placeholders differ (${firstName}: ${left || 'none'}, ${secondName}: ${right || 'none'})`,
      );
    }
  }

  return problems;
}

/** A locale folder (`…/i18n/`) and the messages of each of its language files. */
export interface LocaleFolder {
  folder: string;
  messages: Partial<Record<string, LocaleMessages>>;
}

/**
 * Groups the locale files `import.meta.glob` found (`'./x/i18n/fr-FR.ts'`, `'./x/i18n/en-US.ts'`)
 * by folder, sorted by folder, each with its messages by language.
 */
export function groupLocaleModules(modules: Record<string, LocaleMessages>): LocaleFolder[] {
  const folders = new Map<string, LocaleFolder>();

  for (const [path, messages] of Object.entries(modules)) {
    const slash = path.lastIndexOf('/');
    const folder = path.slice(0, slash);
    const locale = path.slice(slash + 1).replace(/\.[^.]+$/, '');
    const entry = folders.get(folder) ?? { folder, messages: {} };

    entry.messages[locale] = messages;
    folders.set(folder, entry);
  }

  return [...folders.values()].sort((a, b) => a.folder.localeCompare(b.folder));
}
