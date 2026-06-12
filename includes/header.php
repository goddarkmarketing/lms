<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/student_auth.php';
$settings = getSettings();
$pageTitle = $pageTitle ?? getSetting('site_title', 'Wenxin Chinese');
$cartCount = cartCount();
$cartItems = cartItems();
$currentStudent = currentStudent();
$checkoutNavUrl = $cartCount > 0
    ? APP_URL . '/public/checkout.php'
    : APP_URL . '/public/cart.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(getSetting('site_title')) ?></title>
    <meta name="description" content="<?= e(getSetting('site_tagline')) ?>">
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
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=98">
    <link rel="icon" href="<?= e(brandLogoAsset()) ?>" type="image/svg+xml">
</head>
<body>
<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="<?= APP_URL ?>/public/index.php" class="brand">
            <img src="<?= e(brandLogoAsset()) ?>" alt="Wenxin Chinese" class="brand-logo">
            <span class="brand-text">
                <strong>WENXIN</strong>
                <small>CHINESE</small>
            </span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="เปิดเมนู">
            <span></span><span></span><span></span>
        </button>
        <nav class="site-nav" id="siteNav">
            <div class="site-nav-links">
                <a href="<?= APP_URL ?>/public/index.php">หน้าแรก</a>
                <a href="<?= APP_URL ?>/public/courses.php">คอร์สเรียน</a>
                <a href="<?= APP_URL ?>/public/faq.php">คำถามที่พบบ่อย</a>
                <a href="<?= APP_URL ?>/public/contact.php">ติดต่อเรา</a>
            </div>
            <form class="header-search" action="<?= APP_URL ?>/public/courses.php" method="get" role="search">
                <label class="sr-only" for="headerSearch">ค้นหาคอร์ส</label>
                <span class="header-search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M21 21l-4.3-4.3"></path>
                    </svg>
                </span>
                <input
                    id="headerSearch"
                    class="header-search-input"
                    type="search"
                    name="q"
                    placeholder="ค้นหาคอร์ส..."
                    value="<?= e(trim($_GET['q'] ?? '')) ?>"
                    autocomplete="off"
                >
            </form>
            <div class="header-actions">
                <button id="cartToggle" type="button" class="cart-toggle header-action-btn header-btn-cart" aria-controls="cartDrawer" aria-expanded="false">
                    <span class="cart-toggle-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 6h15l-1.5 9h-12z"></path>
                            <path d="M6 6l-2-2"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>
                    </span>
                    <span>ตะกร้า</span>
                    <span class="cart-count" id="cartCount"><?= (int) $cartCount ?></span>
                </button>
                <?php if ($currentStudent): ?>
                <a href="<?= APP_URL ?>/public/profile.php" class="btn btn-primary btn-sm header-action-btn">โปรไฟล์</a>
                <a href="<?= APP_URL ?>/public/student-logout.php" class="btn btn-outline btn-sm header-action-btn">ออกจากระบบ</a>
                <?php else: ?>
                <a href="<?= APP_URL ?>/public/register.php" class="btn btn-sm header-action-btn header-btn-register">สมัครสมาชิก</a>
                <a href="<?= APP_URL ?>/public/login.php" class="btn btn-primary btn-sm header-action-btn header-btn-login">เข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<div class="cart-overlay" id="cartOverlay" aria-hidden="true"></div>
<aside class="cart-drawer" id="cartDrawer" aria-hidden="true" aria-label="ตะกร้าสินค้า">
    <div class="cart-drawer-header">
        <h3>ตะกร้าของคุณ</h3>
        <button type="button" class="cart-close" id="cartCloseBtn" aria-label="ปิดตะกร้า">✕</button>
    </div>
    <div class="cart-drawer-body">
        <?php if (!$cartItems): ?>
            <p class="cart-empty">ยังไม่มีคอร์สในตะกร้า</p>
        <?php else: ?>
            <ul class="cart-list">
                <?php foreach ($cartItems as $item): ?>
                    <li class="cart-item">
                        <div class="cart-item-main">
                            <div class="cart-item-title"><?= e($item['title'] ?? '') ?></div>
                            <div class="cart-item-price"><?= e(formatPrice((float) ($item['price'] ?? 0))) ?></div>
                        </div>
                        <a class="cart-remove" href="<?= APP_URL ?>/public/cart_remove.php?course_id=<?= (int) ($item['id'] ?? 0) ?>&return=<?= urlencode(currentReturnPath()) ?>" title="นำออกจากตะกร้า">ลบ</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="cart-drawer-footer">
        <div class="cart-total-row">
            <span>รวม</span>
            <strong><?= e(formatPrice(cartTotal())) ?></strong>
        </div>
        <a class="btn btn-primary btn-block cart-checkout"
           href="<?= APP_URL ?>/public/cart.php"
           aria-disabled="<?= $cartCount > 0 ? 'false' : 'true' ?>"
           style="<?= $cartCount > 0 ? '' : 'pointer-events:none;opacity:.6;' ?>">
            ดูตะกร้า
        </a>
    </div>
</aside>
<main>
