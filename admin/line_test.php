<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireAdmin();
require_once dirname(__DIR__) . '/includes/line_messaging.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/settings.php#line-oa');
}

verifyCsrf();

$action = $_POST['line_test_action'] ?? '';

if ($action === 'bot_info') {
    $profile = lineOaGetBotProfile();
    if ($profile) {
        $name = (string) ($profile['displayName'] ?? 'LINE OA');
        $basicId = (string) ($profile['basicId'] ?? '');
        $msg = "เชื่อมต่อ LINE สำเร็จ: {$name}";
        if ($basicId !== '') {
            $msg .= " ({$basicId})";
            $stmt = db()->prepare('
                INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ');
            $stmt->execute(['line_oa_basic_id', ltrim($basicId, '@')]);
            resetSettingsCache();
        }
        flash('admin_success', $msg);
    } else {
        flash('admin_error', 'เชื่อมต่อ LINE ไม่สำเร็จ — ตรวจสอบ Channel Access Token ใน storage/logs/line_oa.log');
    }
}

redirect('/admin/settings.php#line-oa');
