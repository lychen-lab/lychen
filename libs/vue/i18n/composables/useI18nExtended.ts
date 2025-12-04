import { useI18n, type UseI18nOptions } from 'vue-i18n';

import { addRootKey } from '../helpers/RootKeyHelper';
import { buildMessagesFromModules } from '../helpers/MessagesHelper';
import type { RouteLocationAsRelativeGeneric } from 'vue-router';

export interface UseCustomI18nOptions extends UseI18nOptions {
  rootKey?: string;
  prefixed?: boolean;
}

// Define the return type explicitly for clarity
export type UseI18nExtendedReturn = ReturnType<typeof useI18n> & {
  i18nRoute: (to: RouteLocationAsRelativeGeneric) => RouteLocationAsRelativeGeneric;
};

export type Config = { messages: UseI18nOptions['messages']; rootKey: string };

export function buildConfig(modules: Record<string, unknown>, rootKey: string): Config {
  return {
    messages: buildMessagesFromModules(modules),
    rootKey,
  } as const;
}

export function usePrefixedI18n(options: Config) {
  return useI18nExtended({ ...options, prefixed: true });
}

export function useI18nExtended(options?: UseCustomI18nOptions): UseI18nExtendedReturn {
  const i18nOptions = { ...options }; // Clone to avoid modifying original

  if (i18nOptions) {
    if (!i18nOptions.useScope) {
      i18nOptions.useScope = 'global';
    }

    if (i18nOptions.rootKey && i18nOptions.messages) {
      // Assuming addRootKey returns a new object or modifies a copy safely
      i18nOptions.messages = addRootKey(i18nOptions.messages, i18nOptions.rootKey);
    }
  }

  const i18n = useI18n(i18nOptions);
  const originalT = i18n.t;
  const originalN = i18n.n;
  const originalD = i18n.d;

  // Define customT using function declaration, matching original t (ComposerTranslation)
  function customT(...args: Parameters<typeof originalT>): ReturnType<typeof originalT> {
    const key = args[0];
    const restArgs = args.slice(1);

    let calculatedKey = key;
    if (i18nOptions?.prefixed && i18nOptions.rootKey) {
      calculatedKey = `${i18nOptions.rootKey}.${key}`;
    }

    // Use Function.prototype.apply to pass arguments dynamically to the original t
    // This correctly handles all overload scenarios (plural, list, named, options)
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    return originalT(calculatedKey, ...(restArgs as [any]));
  }

  function customN(...args: Parameters<typeof originalN>): ReturnType<typeof originalN> {
    const value = args[0];
    const keyOrOptions = args[1]; // Can be string, object, or undefined
    // const locale = args[2]; // Optional locale included in args

    const passThroughArgs = [...args]; // Copy args to potentially modify

    if (typeof keyOrOptions === 'string') {
      if (keyOrOptions === 'percent') {
        // Adjust value for percent calculation (assuming input is basis points)
        passThroughArgs[0] = value / 10000;
      } else if (keyOrOptions === 'currency') {
        // Adjust value for currency calculation (assuming input is cents)
        passThroughArgs[0] = value / 100;
      }
    }

    // Pass potentially modified args to the original n using spread operator
    return originalN(...(passThroughArgs as Parameters<typeof originalN>));
  }

  // Define customD using function declaration, matching original d (ComposerDateTimeFormatting)
  function customD(...args: Parameters<typeof originalD>): ReturnType<typeof originalD> {
    const passThroughArgs = [...args]; // Copy args

    // Pass potentially modified args to the original d using apply
    return originalD(...(passThroughArgs as Parameters<typeof originalD>));
  }

  function i18nRoute(to: Pick<RouteLocationAsRelativeGeneric, 'name' | 'params'>) {
    return {
      ...to,
      params: {
        locale: i18n.locale.value,
        ...to.params,
      },
    };
  }

  return {
    ...i18n, // Spread the original i18n context first (includes locale, etc.)
    i18nRoute,
    t: customT as typeof originalT, // Override t
    n: customN as typeof originalN, // Override n
    d: customD as typeof originalD, // Override d
  };
}
