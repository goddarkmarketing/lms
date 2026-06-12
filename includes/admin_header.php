<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requireAdmin();
$admin = currentAdmin();
$pageTitle = $pageTitle ?? 'แดชบอร์ด';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$navIcon = static function (string $name): string {
    $icons = [
        'dashboard' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
        'courses' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"/><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"/></svg>',
        'lessons' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>',
        'students' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'payments' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
        'quizzes' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
        'coupons' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
        'users' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 8c.36 0 .7.07 1 .2V8a2 2 0 1 1 0 4h-.09c-.2.53-.76 1-1.51 1z"/></svg>',
        'backup' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'settings' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>',
        'external' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
        'logout' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    ];
    return $icons[$name] ?? '';
};

$navLink = static function (string $page, string $href, string $label, string $icon) use ($currentPage, $navIcon): void {
    $active = $currentPage === $page ? ' active' : '';
    echo '<a href="' . e($href) . '" class="' . trim($active) . '">' . $navIcon($icon) . '<span>' . e($label) . '</span></a>';
};
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="<?= e(headingFontAsset()) ?>" as="font" type="font/ttf" crossorigin>
    <style>
      @font-face {
        font-family: 'DB Adman X';
        src: url('<?= headingFontAsset() ?>') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
      }
    </style>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=18">
</head>
<body class="admin-body">
<div class="admin-overlay" id="adminOverlay" aria-hidden="true"></div>
<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= APP_URL ?>/admin/dashboard.php" class="admin-brand">
            <img src="<?= e(brandLogoAsset()) ?>" alt="Logo">
            <span class="admin-brand-text">
                <strong>Wenxin</strong>
                <small>Admin Panel</small>
            </span>
        </a>
        <nav class="admin-nav" aria-label="เมนูหลังบ้าน">
            <div class="admin-nav-group">
                <span class="admin-nav-label">ภาพรวม</span>
                <?php $navLink('dashboard', APP_URL . '/admin/dashboard.php', 'แดชบอร์ด', 'dashboard'); ?>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">เนื้อหา</span>
                <?php $navLink('courses', APP_URL . '/admin/courses.php', 'คอร์ส', 'courses'); ?>
                <?php $navLink('lessons', APP_URL . '/admin/lessons.php', 'บทเรียน', 'lessons'); ?>
                <?php $navLink('quizzes', APP_URL . '/admin/quizzes.php', 'แบบทดสอบ', 'quizzes'); ?>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">ลูกค้า & การขาย</span>
                <?php $navLink('students', APP_URL . '/admin/students.php', 'นักเรียน', 'students'); ?>
                <?php $navLink('payments', APP_URL . '/admin/payments.php', 'การชำระเงิน', 'payments'); ?>
                <?php $navLink('coupons', APP_URL . '/admin/coupons.php', 'คูปอง', 'coupons'); ?>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">ระบบ</span>
                <?php $navLink('users', APP_URL . '/admin/users.php', 'ผู้ดูแล', 'users'); ?>
                <?php $navLink('backup', APP_URL . '/admin/backup.php', 'สำรองข้อมูล', 'backup'); ?>
                <?php $navLink('settings', APP_URL . '/admin/settings.php', 'ตั้งค่า', 'settings'); ?>
            </div>
            <div class="admin-nav-divider"></div>
            <a href="<?= APP_URL ?>/public/index.php" target="_blank" rel="noopener"><?= $navIcon('external') ?><span>ดูเว็บไซต์</span></a>
            <a href="<?= APP_URL ?>/admin/logout.php" class="logout"><?= $navIcon('logout') ?><span>ออกจากระบบ</span></a>
        </nav>
        <div class="admin-user">
            <small>เข้าสู่ระบบโดย</small>
            <strong><?= e($admin['full_name'] ?? $admin['username'] ?? 'Admin') ?></strong>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-inner">
                <button type="button" class="admin-menu-toggle" id="adminMenuToggle" aria-label="เปิดเมนู" aria-controls="adminSidebar" aria-expanded="false">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
                <h1><?= e($pageTitle) ?></h1>
                <div class="admin-topbar-meta">
                    <span class="admin-topbar-pill"><?= e($admin['username'] ?? 'admin') ?></span>
                </div>
            </div>
        </header>
        <div class="admin-content">
            <div class="admin-content-inner">
