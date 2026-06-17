<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requireAdmin();
$admin = currentAdmin();
$pageTitle = $pageTitle ?? 'แดชบอร์ด';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$navIcon = static function (string $name): string {
    $map = [
        'dashboard' => 'layout-dashboard',
        'courses' => 'book-open',
        'lessons' => 'circle-play',
        'students' => 'users',
        'payments' => 'credit-card',
        'quizzes' => 'clipboard-check',
        'games' => 'gamepad-2',
        'announcements' => 'megaphone',
        'content' => 'file-text',
        'coupons' => 'ticket',
        'users' => 'shield-check',
        'backup' => 'database',
        'settings' => 'settings',
        'external' => 'external-link',
        'logout' => 'log-out',
    ];
    return lucide_icon($map[$name] ?? 'circle-help', ['class' => 'nav-icon', 'stroke' => '1.75']);
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
    <?php require __DIR__ . '/views/fonts_head.php'; ?>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=33">
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
                <?php $navLink('games', APP_URL . '/admin/games.php', 'เกมฝึกฝน', 'games'); ?>
                <?php $navLink('announcements', APP_URL . '/admin/announcements.php', 'ประชาสัมพันธ์', 'announcements'); ?>
                <?php $navLink('content', APP_URL . '/admin/content.php', 'เนื้อหาเว็บ', 'content'); ?>
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
                    <?= lucide_icon('menu', ['size' => 20]) ?>
                </button>
                <h1><?= e($pageTitle) ?></h1>
                <div class="admin-topbar-meta">
                    <span class="admin-topbar-pill"><?= e($admin['username'] ?? 'admin') ?></span>
                </div>
            </div>
        </header>
        <div class="admin-content">
            <div class="admin-content-inner">
