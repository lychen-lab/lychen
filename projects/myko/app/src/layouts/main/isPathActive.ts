/**
 * Whether `currentPath` is `path` or below it: `/logs/1/edit` is under `/logs`, `/logsheet` is
 * not. `/` is only itself, every path being below it.
 */
export function isPathActive(currentPath: string, path: string): boolean {
  if (path === '/') {
    return currentPath === '/';
  }

  return currentPath === path || currentPath.startsWith(`${path}/`);
}
