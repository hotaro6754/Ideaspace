import { test, expect } from '@playwright/test';

test('landing page visual check', async ({ page }) => {
  await page.goto('http://localhost:8080');
  await page.screenshot({ path: 'landing.png', fullPage: true });
  await expect(page).toHaveTitle(/IdeaSync/);
});

test('dashboard visual check', async ({ page }) => {
  // Mock login? Actually let's just check the page exists
  await page.goto('http://localhost:8080/?page=ideas');
  await page.screenshot({ path: 'ideas_list.png', fullPage: true });
});
