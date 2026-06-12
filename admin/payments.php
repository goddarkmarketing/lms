<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireAdmin();
require_once dirname(__DIR__) . '/includes/checkout_flow.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['status'])) {
    verifyCsrf();
    $paymentId = (int) $_POST['payment_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'verified', 'rejected'], true)) {
        if ($status === 'verified') {
            $fetch = db()->prepare('SELECT * FROM payments WHERE id = ? LIMIT 1');
            $fetch->execute([$paymentId]);
            $payment = $fetch->fetch();
            if ($payment && ($payment['status'] ?? '') !== 'verified') {
                enrollFromPayment($payment);
            }
        }
        $stmt = db()->prepare('UPDATE payments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $paymentId]);
        flash('admin_success', $status === 'verified' ? 'ยืนยันและเปิดสิทธิ์เรียนเรียบร้อย' : 'อัปเดตสถานะเรียบร้อย');
    }
    redirect('/admin/payments.php');
}

$pageTitle = 'การชำระเงิน';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$message = flash('admin_success');

$stmt = db()->query('
    SELECT p.*, c.title AS course_title
    FROM payments p
    LEFT JOIN courses c ON c.id = p.course_id
    ORDER BY p.created_at DESC
');
$payments = $stmt->fetchAll();

$paymentItemsMap = [];
foreach ($payments as $p) {
    $paymentItemsMap[(int) $p['id']] = getPaymentItemsWithTitles((int) $p['id']);
}

$statusLabels = [
    'pending' => 'รอตรวจสอบ',
    'verified' => 'ยืนยันแล้ว',
    'rejected' => 'ปฏิเสธ',
];
?>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>รายการแจ้งชำระเงิน</h2>
    </div>
    <div class="admin-card-body is-flush">
        <table class="data-table">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>ชื่อ</th>
                    <th>โทร</th>
                    <th>คอร์ส</th>
                    <th>จำนวน</th>
                    <th>สลิป</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <?php
                    $items = $paymentItemsMap[(int) $p['id']] ?? [];
                    $paymentStatus = (string) ($p['status'] ?? 'pending');
                ?>
                <tr>
                    <td><?= e(date('d/m/Y H:i', strtotime($p['created_at']))) ?></td>
                    <td><?= e($p['student_name']) ?><br><small><?= e($p['student_email'] ?? '') ?></small></td>
                    <td><?= e($p['student_phone']) ?></td>
                    <td>
                        <?php if ($items): ?>
                            <ul class="payment-course-list">
                                <?php foreach ($items as $item): ?>
                                <li><?= e($item['title']) ?> <small>(<?= e(formatPrice((float) $item['amount'])) ?>)</small></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= e($p['course_title'] ?? '-') ?>
                        <?php endif; ?>
                    </td>
                    <td><?= e(formatPrice((float) $p['amount'])) ?></td>
                    <td>
                        <?php if ($p['slip_image']): ?>
                            <a href="<?= APP_URL ?>/public/view_slip.php?id=<?= (int) $p['id'] ?>" target="_blank">ดูสลิป</a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= e($paymentStatus) ?>"><?= e($statusLabels[$paymentStatus] ?? $paymentStatus) ?></span></td>
                    <td>
                        <?php if ($paymentStatus === 'pending'): ?>
                        <form method="post" class="payment-action-form">
                            <?= csrfField() ?>
                            <input type="hidden" name="payment_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" name="status" value="verified" class="btn btn-primary btn-sm">ยืนยัน</button>
                            <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">ปฏิเสธ</button>
                        </form>
                        <?php elseif ($paymentStatus === 'rejected'): ?>
                        <form method="post" class="payment-action-form">
                            <?= csrfField() ?>
                            <input type="hidden" name="payment_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" name="status" value="verified" class="btn btn-primary btn-sm">ยืนยัน</button>
                        </form>
                        <?php else: ?>
                        <span class="payment-action-done">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($p['note']): ?>
                <tr><td colspan="8" class="payment-note-row">หมายเหตุ: <?= e($p['note']) ?></td></tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!$payments): ?>
        <p class="table-empty">ยังไม่มีรายการ</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
