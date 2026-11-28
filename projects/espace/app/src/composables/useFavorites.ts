import { readonly, ref } from 'vue';

const STORAGE_KEY = 'espace:favorite-area-proposals';

function read(): string[] {
  try {
    const stored: unknown = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]');
    return Array.isArray(stored) ? stored.filter((uuid) => typeof uuid === 'string') : [];
  } catch {
    return [];
  }
}

// One state for the whole app, so the list, the detail and the Favorites screen agree.
const favorites = ref<string[]>(read());

function save(uuids: string[]) {
  favorites.value = uuids;
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(uuids));
  } catch {
    // Storage refused (private browsing, quota): the favorites last as long as the page.
  }
}

// Another tab of the same browser changed them.
window.addEventListener('storage', (event) => {
  if (event.key === STORAGE_KEY) {
    favorites.value = read();
  }
});

/**
 * Favorite area proposals, as UUIDs. The API has no favorites yet: they are kept in this
 * browser's localStorage, so they are local to the device and not tied to the account.
 */
export function useFavorites() {
  function isFavorite(uuid: string): boolean {
    return favorites.value.includes(uuid);
  }

  function removeFavorite(uuid: string) {
    save(favorites.value.filter((favorite) => favorite !== uuid));
  }

  function toggleFavorite(uuid: string) {
    if (isFavorite(uuid)) {
      removeFavorite(uuid);
    } else {
      save([...favorites.value, uuid]);
    }
  }

  return { favorites: readonly(favorites), isFavorite, removeFavorite, toggleFavorite };
}
