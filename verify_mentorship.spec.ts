import { test, expect } from '@playwright/test';

test('Mentorship page and sidebar verification', async ({ page }) => {
  // Mocking Supabase calls if necessary, but for a simple UI check we can just visit
  // Since the build succeeded and it's static, we can check the layout

  await page.goto('http://localhost:3000/mentorship');

  // Check for Header
  await expect(page.locator('header')).toContainText('Mentorship Hub');

  // Check for Sidebar link
  await expect(page.locator('nav')).toContainText('Mentors');

  // Take a screenshot
  await page.screenshot({ path: 'mentorship_page.png', fullPage: true });

  // Check for request guidance button (even if list is empty, the empty state might have something)
  // But we expect mentors if seed data exists.
});
