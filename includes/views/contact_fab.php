<?php

declare(strict_types=1);

/**
 * Floating contact button + popover panel.
 * Expects: $footerLineUrl, $footerLineId, $footerFacebook, $footerPhone,
 *          $footerEmail, $footerYoutube, $footerTiktok
 */

$contactFabItems = [];

if ($footerLineId !== '') {
    $contactFabItems[] = [
        'label' => 'LINE',
        'value' => $footerLineId,
        'href' => $footerLineUrl,
        'external' => true,
        'icon' => 'line',
        'tone' => 'line',
    ];
}

if ($footerFacebook !== '') {
    $contactFabItems[] = [
        'label' => 'Facebook',
        'value' => 'Facebook',
        'href' => $footerFacebook,
        'external' => true,
        'icon' => 'facebook',
        'tone' => 'fb',
    ];
}

if ($footerTiktok !== '') {
    $contactFabItems[] = [
        'label' => 'TikTok',
        'value' => 'TikTok',
        'href' => $footerTiktok,
        'external' => true,
        'icon' => 'tiktok',
        'tone' => 'tiktok',
    ];
}

if ($footerYoutube !== '') {
    $contactFabItems[] = [
        'label' => 'YouTube',
        'value' => 'YouTube',
        'href' => $footerYoutube,
        'external' => true,
        'icon' => 'youtube',
        'tone' => 'youtube',
    ];
}

if ($footerPhone !== '') {
    $contactFabItems[] = [
        'label' => 'โทรศัพท์',
        'value' => $footerPhone,
        'href' => 'tel:' . preg_replace('/\s+/', '', $footerPhone),
        'external' => false,
        'icon' => 'phone',
        'tone' => 'phone',
    ];
}

if ($footerEmail !== '') {
    $contactFabItems[] = [
        'label' => 'อีเมล',
        'value' => $footerEmail,
        'href' => 'mailto:' . $footerEmail,
        'external' => false,
        'icon' => 'email',
        'tone' => 'email',
    ];
}

if (!$contactFabItems) {
    return;
}

$contactFabMascotPath = dirname(__DIR__, 2) . '/assets/images/contact/contact-fab-mascot.png';
$contactFabMascotUrl = asset('images/contact/contact-fab-mascot.png');
if (is_file($contactFabMascotPath)) {
    $contactFabMascotUrl .= '?v=' . (string) filemtime($contactFabMascotPath);
}

?>
<div class="contact-fab" id="contactFab">
    <div class="contact-fab-panel" id="contactFabPanel" role="dialog" aria-label="ช่องทางติดต่อ" aria-hidden="true">
        <div class="contact-fab-head">
            <strong>ติดต่อเรา</strong>
            <button type="button" class="contact-fab-close" id="contactFabClose" aria-label="ปิด">
                <?= lucide_icon('x', ['size' => 18, 'stroke' => '2.5']) ?>
            </button>
        </div>
        <ul class="contact-fab-list">
            <?php foreach ($contactFabItems as $item): ?>
            <li>
                <a href="<?= e($item['href']) ?>" class="contact-fab-link contact-fab-link--<?= e($item['tone']) ?>"<?= $item['external'] ? ' target="_blank" rel="noopener"' : '' ?>>
                    <span class="contact-fab-icon" aria-hidden="true">
                        <?= contactChannelIcon((string) $item['tone'], 18) ?>
                    </span>
                    <span class="contact-fab-text">
                        <span class="contact-fab-label"><?= e($item['label']) ?></span>
                        <span class="contact-fab-value"><?= e($item['value']) ?></span>
                    </span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="contact-fab-anchor">
        <div class="contact-fab-bubble" id="contactFabBubble" role="status" aria-live="polite" aria-hidden="true">
            <button type="button" class="contact-fab-bubble-close" id="contactFabBubbleClose" aria-label="ปิดข้อความ">
                <?= lucide_icon('x', ['size' => 14, 'stroke' => '2.5']) ?>
            </button>
            <p class="contact-fab-bubble-text"><strong>น้องหน่อไม้</strong>สวัสดีครับ มีอะไรให้ช่วยเหลือไหมครับ?</p>
        </div>
        <button
            type="button"
            class="contact-fab-hit"
            id="contactFabTrigger"
            aria-expanded="false"
            aria-controls="contactFabPanel"
            aria-label="ติดต่อเรา"
        ></button>
        <img
            src="<?= e($contactFabMascotUrl) ?>"
            class="contact-fab-mascot"
            alt=""
            width="128"
            height="128"
            loading="lazy"
            decoding="async"
            aria-hidden="true"
        >
    </div>
</div>
