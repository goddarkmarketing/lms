<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'นโยบายความเป็นส่วนตัว';
require_once dirname(__DIR__) . '/includes/header.php';

$custom = trim(getSetting('privacy_policy_html', ''));
?>

<section class="container legal-page" style="padding:2rem 0 3rem;max-width:800px">
    <h1>นโยบายความเป็นส่วนตัว (PDPA)</h1>
    <div class="legal-content" style="line-height:1.8;color:var(--gray-700)">
        <?php if ($custom !== ''): ?>
            <?= $custom ?>
        <?php else: ?>
            <p><strong>อัปเดตล่าสุด:</strong> <?= date('d/m/Y') ?></p>
            <p><?= e(getSetting('site_title', 'Wenxin Chinese')) ?> ("เรา") ให้ความสำคัญกับการคุ้มครองข้อมูลส่วนบุคคลของท่าน ตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA)</p>

            <h2>ข้อมูลที่เราเก็บรวบรวม</h2>
            <ul>
                <li>ชื่อ-นามสกุล เบอร์โทร อีเมล Line ID</li>
                <li>ข้อมูลการชำระเงินและหลักฐานการโอนเงิน</li>
                <li>ความคืบหน้าการเรียน ผลแบบทดสอบ และใบประกาศนียบัตร</li>
            </ul>

            <h2>วัตถุประสงค์ในการใช้ข้อมูล</h2>
            <ul>
                <li>เปิดสิทธิ์เรียนและให้บริการคอร์สออนไลน์</li>
                <li>ติดต่อสื่อสารเกี่ยวกับการสมัครเรียนและการชำระเงิน</li>
                <li>ปรับปรุงคุณภาพการสอนและบริการ</li>
            </ul>

            <h2>การเปิดเผยข้อมูล</h2>
            <p>เราจะไม่ขายหรือเผยแพร่ข้อมูลส่วนบุคคลของท่านแก่บุคคลภายนอก เว้นแต่ได้รับความยินยอมหรือกฎหมายกำหนด</p>

            <h2>สิทธิของเจ้าของข้อมูล</h2>
            <p>ท่านมีสิทธิขอเข้าถึง แก้ไข ลบ หรือถอนความยินยอม โดยติดต่อ <?= e(getSetting('phone', '')) ?> หรือ Line: <?= e(getSetting('line_id', '')) ?></p>

            <h2>การรักษาความปลอดภัย</h2>
            <p>เราใช้มาตรการทางเทคนิคที่เหมาะสม เช่น การเข้ารหัสรหัสผ่าน และการจำกัดสิทธิ์เข้าถึงข้อมูล</p>
        <?php endif; ?>
    </div>
    <p style="margin-top:2rem"><a href="<?= APP_URL ?>/public/index.php">← กลับหน้าแรก</a></p>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
