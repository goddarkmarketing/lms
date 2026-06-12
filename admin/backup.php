<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/backup.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'download') {
    verifyCsrf();
    $sql = exportDatabaseSql();
    $filename = 'wenxin_lms_' . date('Y-m-d_His') . '.sql';
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql));
    echo $sql;
    exit;
}

$pageTitle = 'สำรองข้อมูล';
require_once dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header"><h2>สำรองฐานข้อมูล</h2></div>
    <div class="admin-card-body">
        <p>ดาวน์โหลดไฟล์ SQL สำรองข้อมูลทั้งหมดของระบบ แนะนำให้สำรองเป็นประจำก่อนอัปเดตหรือแก้ไขข้อมูลสำคัญ</p>
        <p style="color:#6b7280;font-size:.9rem;margin:1rem 0">
            ไฟล์จะรวมตารางทั้งหมด รวมถึงนักเรียน การชำระเงิน คอร์ส และการตั้งค่า
        </p>
        <form method="post">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="download">
            <button type="submit" class="btn btn-primary">ดาวน์โหลดไฟล์สำรอง (.sql)</button>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2>คำแนะนำ</h2></div>
    <div class="admin-card-body">
        <ul style="line-height:1.8;color:#4b5563">
            <li>เก็บไฟล์สำรองในที่ปลอดภัย อย่าแชร์สาธารณะ (มีข้อมูลส่วนตัว)</li>
            <li>บน production ควรตั้ง cron สำรองอัตโนมัติทุกวัน</li>
            <li>ทดสอบ restore บนเครื่องทดสอบก่อนใช้จริง</li>
        </ul>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
