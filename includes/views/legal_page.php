<?php

declare(strict_types=1);

$legalBreadcrumbLabel = $legalBreadcrumbLabel ?? '';
$legalTitle = $legalTitle ?? '';
$legalLead = $legalLead ?? '';
$legalActiveTab = $legalActiveTab ?? '';
$legalUpdated = $legalUpdated ?? date('d/m/Y');
$legalContent = $legalContent ?? '';

$termsUrl = APP_URL . '/public/terms.php';
$privacyUrl = APP_URL . '/public/privacy.php';
$contactUrl = APP_URL . '/public/contact.php';
$homeUrl = APP_URL . '/public/index.php';
?>

<header class="page-header legal-page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumb">
            <a href="<?= e($homeUrl) ?>">หน้าแรก</a>
            <span aria-hidden="true">/</span>
            <span><?= e($legalBreadcrumbLabel) ?></span>
        </nav>
        <h1><?= e($legalTitle) ?></h1>
        <?php if ($legalLead !== ''): ?>
            <p><?= e($legalLead) ?></p>
        <?php endif; ?>
    </div>
</header>

<section class="legal-page-section">
    <div class="container">
        <div class="legal-page-toolbar">
            <p class="legal-page-meta">
                <span class="legal-page-meta-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </span>
                อัปเดตล่าสุด: <?= e($legalUpdated) ?>
            </p>
            <nav class="legal-page-tabs" aria-label="เอกสารทางกฎหมาย">
                <a
                    href="<?= e($termsUrl) ?>"
                    class="legal-page-tab<?= $legalActiveTab === 'terms' ? ' is-active' : '' ?>"
                    <?= $legalActiveTab === 'terms' ? 'aria-current="page"' : '' ?>
                >ข้อกำหนดการใช้งาน</a>
                <a
                    href="<?= e($privacyUrl) ?>"
                    class="legal-page-tab<?= $legalActiveTab === 'privacy' ? ' is-active' : '' ?>"
                    <?= $legalActiveTab === 'privacy' ? 'aria-current="page"' : '' ?>
                >นโยบายความเป็นส่วนตัว</a>
            </nav>
        </div>

        <article class="legal-page-card">
            <div class="legal-content prose-legal">
                <?= $legalContent ?>
            </div>
        </article>

        <div class="legal-page-actions">
            <a href="<?= e($homeUrl) ?>" class="btn btn-secondary">กลับหน้าแรก</a>
            <a href="<?= e($contactUrl) ?>" class="btn btn-primary">ติดต่อเรา</a>
        </div>
    </div>
</section>
