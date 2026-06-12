<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'ข้อกำหนดการใช้งาน';
require_once dirname(__DIR__) . '/includes/header.php';

$custom = trim(getSetting('terms_html', ''));
?>

<section class="container legal-page" style="padding:2rem 0 3rem;max-width:800px">
    <h1>ข้อกำหนดและเงื่อนไขการใช้งาน</h1>
    <div class="legal-content" style="line-height:1.8;color:var(--gray-700)">
        <?php if ($custom !== ''): ?>
            <?= $custom ?>
        <?php else: ?>
            <p><strong>อัปเดตล่าสุด:</strong> <?= date('d/m/Y') ?></p>
            <p>การใช้งานเว็บไซต์และบริการของ <?= e(getSetting('site_title', 'Wenxin Chinese')) ?> ถือว่าท่านยอมรับข้อกำหนดดังต่อไปนี้</p>

            <h2>การสมัครและบัญชีผู้ใช้</h2>
            <ul>
                <li>ผู้เรียนต้องให้ข้อมูลที่ถูกต้องและรักษาความลับของรหัสผ่าน</li>
                <li>ห้ามแชร์บัญชีหรือสิทธิ์เรียนให้ผู้อื่น</li>
            </ul>

            <h2>การชำระเงินและการคืนเงิน</h2>
            <ul>
                <li>คอร์สจะเปิดสิทธิ์เรียนหลังยืนยันการชำระเงินโดยทีมงาน</li>
                <li>นโยบายการคืนเงินเป็นไปตามที่ทีมงานแจ้งก่อนชำระเงิน</li>
            </ul>

            <h2>ทรัพย์สินทางปัญญา</h2>
            <p>เนื้อหาคอร์ส วิดีโอ เอกสาร และสื่อการเรียนทั้งหมดเป็นทรัพย์สินของผู้ให้บริการ ห้ามคัดลอก แจกจ่าย หรือเผยแพร่โดยไม่ได้รับอนุญาต</p>

            <h2>การระงับบริการ</h2>
            <p>เราขอสงวนสิทธิ์ระงับบัญชีที่ละเมิดข้อกำหนด หรือใช้บริการในทางที่ผิดกฎหมาย</p>

            <h2>ติดต่อเรา</h2>
            <p>โทร <?= e(getSetting('phone', '')) ?> | Line: <?= e(getSetting('line_id', '')) ?></p>
        <?php endif; ?>
    </div>
    <p style="margin-top:2rem"><a href="<?= APP_URL ?>/public/index.php">← กลับหน้าแรก</a></p>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
