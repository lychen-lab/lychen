import { expect } from '@playwright/test';
import { test } from '../../fixtures/fixtures';

test('first attempt', async ({ person1 }) => {
  await person1.page.goto('dashboard');
  await expect(person1.page.getByText('Tableau de bord')).toBeVisible();
  await expect(person1.page.getByText('Bonjour, Marianne')).toBeVisible();
});
