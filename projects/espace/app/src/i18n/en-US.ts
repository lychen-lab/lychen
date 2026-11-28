export default {
  navigation: {
    home: 'Home',
    proposals: 'Spaces',
    requests: 'Requests',
    favorites: 'Favorites',
  },
  // The codes are those of the API's AreaActivity resource.
  activities: {
    general_maintenance: 'General maintenance',
    gardening: 'Gardening',
    beehives: 'Beehives',
    vegetable_sharing: 'Vegetable sharing',
    fruit_sharing: 'Fruit sharing',
    flower_planting: 'Flower planting',
    tree_planting: 'Tree planting',
  },
  state: {
    loading: 'Loading…',
    error: 'Unable to load this information.',
    retry: 'Try again',
  },
  filters: {
    city: 'Search by city',
    activity: 'Activity',
    all_activities: 'All activities',
    order: 'Sort',
    newest_first: 'Newest first',
    oldest_first: 'Oldest first',
    reset: 'Clear filters',
  },
  pagination: {
    label: 'Pagination',
    previous: 'Previous page',
    next: 'Next page',
    position: 'Page {page} of {total}',
  },
  favorite: {
    add: 'Add to favorites',
    remove: 'Remove from favorites',
  },
  share: {
    action: 'Share',
    copied: 'Link copied to the clipboard.',
    failed: 'Unable to share or copy the link.',
  },
  back: 'Back',
  image_alt: 'Generic illustration: no photo is available',
};
