<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/homepage.php';

$pageTitle = 'ติดต่อเรา';
require_once dirname(__DIR__) . '/includes/header.php';

$lineUrl = lineContactUrl();
$lineId = trim(getSetting('line_id', ''));
$phone = trim(getSetting('phone', ''));
$email = trim(getSetting('email_admin', ''));
$facebook = trim(getSetting('facebook_url', ''));
$youtube = trim(getSetting('youtube_url', ''));
$tiktok = trim(getSetting('tiktok_url', ''));
$tagline = getSetting('site_tagline', 'สถาบันสอนภาษาจีนออนไลน์ คุณภาพ เรียนง่าย ได้ผลจริง');
$checkoutUrl = $checkoutNavUrl ?? APP_URL . '/public/cart.php';

$contactChannels = [];

if ($lineId !== '') {
    $contactChannels[] = [
        'featured' => true,
        'tone' => 'line',
        'label' => 'LINE Official',
        'title' => 'แชทกับทีมงานทันที',
        'value' => $lineId,
        'hint' => 'ตอบเร็วที่สุด — สอบถามคอร์ส ราคา และการสมัครเรียน',
        'href' => $lineUrl,
        'external' => true,
        'cta' => 'แอด Line',
    ];
}

if ($phone !== '') {
    $contactChannels[] = [
        'tone' => 'phone',
        'label' => 'โทรศัพท์',
        'title' => 'โทรหาทีมงาน',
        'value' => $phone,
        'hint' => 'จันทร์–อาทิตย์ 10:00–20:00 น.',
        'href' => 'tel:' . preg_replace('/\s+/', '', $phone),
        'external' => false,
        'cta' => 'โทรเลย',
    ];
}

if ($email !== '') {
    $contactChannels[] = [
        'tone' => 'email',
        'label' => 'อีเมล',
        'title' => 'ส่งอีเมลถึงเรา',
        'value' => $email,
        'hint' => 'เหมาะสำหรับสอบถามรายละเอียดหรือเอกสารแนบ',
        'href' => 'mailto:' . $email,
        'external' => false,
        'cta' => 'ส่งอีเมล',
    ];
}

if ($facebook !== '') {
    $contactChannels[] = [
        'tone' => 'facebook',
        'label' => 'Facebook',
        'title' => 'ติดตามข่าวสาร',
        'value' => 'Wenxin Chinese',
        'hint' => 'อัปเดตคอร์ส กิจกรรม และเทคนิคการเรียน',
        'href' => $facebook,
        'external' => true,
        'cta' => 'ไปที่ Facebook',
    ];
}

if ($youtube !== '') {
    $contactChannels[] = [
        'tone' => 'youtube',
        'label' => 'YouTube',
        'title' => 'ชมคลิปสอน',
        'value' => 'YouTube Channel',
        'hint' => 'ตัวอย่างบทเรียนและเทคนิคภาษาจีน',
        'href' => $youtube,
        'external' => true,
        'cta' => 'ดูช่อง YouTube',
    ];
}

if ($tiktok !== '') {
    $contactChannels[] = [
        'tone' => 'tiktok',
        'label' => 'TikTok',
        'title' => 'คลิปสั้นน่ารู้',
        'value' => 'TikTok',
        'hint' => 'เรียนรู้ภาษาจีนแบบสนุกในคลิปสั้น',
        'href' => $tiktok,
        'external' => true,
        'cta' => 'ดู TikTok',
    ];
}

?>

<header class="page-header contact-page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumb">
            <a href="<?= APP_URL ?>/public/index.php">หน้าแรก</a>
            <span aria-hidden="true">/</span>
            <span>ติดต่อเรา</span>
        </nav>
        <h1>ติดต่อเรา</h1>
        <p>ทีมงาน Wenxin Chinese พร้อมให้คำปรึกษาเรื่องคอร์ส การสมัครเรียน และการชำระเงิน</p>
    </div>
</header>

<section class="section contact-page-section" id="contact">
    <div class="container contact-page-layout">
        <div class="contact-page-intro-card">
            <p class="contact-page-eyebrow">WENXIN CHINESE</p>
            <h2>มีคำถาม? เราช่วยได้</h2>
            <p class="contact-page-lead"><?= e($tagline) ?> เลือกช่องทางที่สะดวกที่สุด ทีมงานจะตอบกลับโดยเร็วที่สุด</p>

            <ul class="contact-page-perks">
                <li>
                    <span class="contact-page-perk-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </span>
                    <span>ปรึกษาคอร์ส HSK และการสอบได้ฟรี</span>
                </li>
                <li>
                    <span class="contact-page-perk-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </span>
                    <span>ตอบกลับภายใน 24 ชั่วโมง (วันทำการ)</span>
                </li>
                <li>
                    <span class="contact-page-perk-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <span>ข้อมูลของคุณปลอดภัย ไม่เปิดเผยต่อบุคคลที่สาม</span>
                </li>
            </ul>

            <div class="contact-page-quick-links">
                <a href="<?= APP_URL ?>/public/faq.php" class="contact-page-quick-link">
                    <strong>คำถามที่พบบ่อย</strong>
                    <span>ดูคำตอบก่อนติดต่อ</span>
                </a>
                <a href="<?= e($checkoutUrl) ?>" class="contact-page-quick-link">
                    <strong>แจ้งชำระเงิน</strong>
                    <span>ส่งสลิปหลังโอนเงิน</span>
                </a>
            </div>
        </div>

        <?php if ($contactChannels): ?>
        <div class="contact-page-channels-panel">
            <?php foreach ($contactChannels as $channel): ?>
            <?php include dirname(__DIR__) . '/includes/views/contact_channel_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="contact-page-empty contact-box">
            <h2>ยังไม่ได้ตั้งค่าช่องทางติดต่อ</h2>
            <p>กรุณาติดต่อผู้ดูแลระบบ หรือดูคำถามที่พบบ่อยก่อน</p>
            <a href="<?= APP_URL ?>/public/faq.php" class="btn btn-primary">ไปหน้า FAQ</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="contact-page-bottom">
    <div class="container contact-page-bottom-inner">
        <div>
            <h2>พร้อมเริ่มเรียนแล้ว?</h2>
            <p>เลือกคอร์สที่เหมาะกับระดับของคุณ แล้วสมัครเรียนได้ทันที</p>
        </div>
        <div class="contact-page-bottom-actions">
            <a href="<?= APP_URL ?>/public/courses.php" class="btn btn-gold btn-lg">ดูคอร์สทั้งหมด</a>
            <a href="<?= APP_URL ?>/public/register.php" class="btn btn-hero-outline btn-lg">วิธีสมัครเรียน</a>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
