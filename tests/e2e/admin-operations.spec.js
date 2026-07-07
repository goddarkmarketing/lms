// @ts-check
const { test, expect } = require('@playwright/test');
const { loginAdmin } = require('../helpers/auth');

test.describe('ผู้ดูแล — จัดการรายการชำระเงินและนักเรียน', () => {
  test.beforeEach(async ({ page }) => {
    await loginAdmin(page);
  });

  test('หน้าชำระเงิน: โหลดรายการและมีปุ่มยืนยัน/ปฏิเสธสำหรับรายการรอตรวจสอบ', async ({ page }) => {
    await page.goto('admin/payments.php');
    await expect(page.locator('.admin-card-header h2')).toContainText('รายการแจ้งชำระเงิน');

    const pendingRow = page.locator('table.payments-table tbody tr', { has: page.locator('.badge-pending') }).first();
    if ((await pendingRow.count()) === 0) {
      test.skip(true, 'ไม่มีรายการรอตรวจสอบ — รัน e2e student-admin-flow ก่อน');
    }

    await expect(pendingRow.locator('button[name="status"][value="verified"]')).toBeVisible();
    await expect(pendingRow.locator('button[name="status"][value="rejected"]')).toBeVisible();
  });

  test('หน้านักเรียน: เปิด modal จัดการสิทธิ์ได้', async ({ page }) => {
    await page.goto('admin/students.php');
    const manageBtn = page.locator('[data-open-student]').first();
    test.skip((await manageBtn.count()) === 0, 'ไม่มีนักเรียนในระบบ');

    await manageBtn.click();
    await expect(page.locator('#studentManageModal')).toBeVisible();
    const activePanel = page.locator('.student-modal-panel:not([hidden])');
    await expect(activePanel).toBeVisible();
    await expect(activePanel.locator('.student-enroll-form select[name="course_id"]')).toBeVisible();
  });

  test('หน้าการจอง: โหลดได้', async ({ page }) => {
    const response = await page.goto('admin/bookings.php');
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('.admin-content')).toBeVisible();
  });

  test('หน้ารอบเรียน Live: โหลดได้', async ({ page }) => {
    const response = await page.goto('admin/sessions.php');
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('.admin-content')).toBeVisible();
  });

  test('หน้าตั้งค่า LINE OA: โหลดส่วน integration', async ({ page }) => {
    await page.goto('admin/settings.php#line-oa');
    await expect(page.locator('#line-oa')).toBeVisible();
    await expect(page.locator('input[name="line_oa_channel_secret"]')).toBeVisible();
    await expect(page.locator('button[name="line_test_action"]')).toBeVisible();
  });
});
