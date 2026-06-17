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
        <?php if ($payments): ?>
        <div class="table-responsive">
        <table class="admin-table payments-table">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>ลูกค้า</th>
                    <th>โทร</th>
                    <th>คอร์ส</th>
                    <th class="col-amount">จำนวน</th>
                    <th class="col-slip">สลิป</th>
                    <th>สถานะ</th>
                    <th class="actions">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <?php
                    $items = $paymentItemsMap[(int) $p['id']] ?? [];
                    $paymentStatus = (string) ($p['status'] ?? 'pending');
                    $slipFilename = (string) ($p['slip_image'] ?? '');
                    $slipUrl = $slipFilename !== ''
                        ? APP_URL . '/public/view_slip.php?id=' . (int) $p['id']
                        : '';
                    $slipExt = strtolower(pathinfo(basename($slipFilename), PATHINFO_EXTENSION));
                    $slipIsImage = in_array($slipExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                ?>
                <tr>
                    <td class="payment-date"><?= e(date('d/m/Y H:i', strtotime($p['created_at']))) ?></td>
                    <td class="payment-payer">
                        <strong><?= e($p['student_name']) ?></strong>
                        <?php if (!empty($p['student_email'])): ?>
                            <span><?= e($p['student_email']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="payment-phone"><?= e($p['student_phone']) ?></td>
                    <td class="payment-courses">
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
                    <td class="payment-amount"><?= e(formatPrice((float) $p['amount'])) ?></td>
                    <td class="col-slip">
                        <?php if ($slipUrl !== ''): ?>
                            <button
                                type="button"
                                class="btn btn-outline btn-sm payment-slip-btn"
                                data-open-slip
                                data-slip-url="<?= e($slipUrl) ?>"
                                data-slip-image="<?= $slipIsImage ? '1' : '0' ?>"
                                data-slip-payer="<?= e($p['student_name']) ?>"
                            >ดูสลิป</button>
                        <?php else: ?>
                            <span class="payment-slip-missing">—</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= e($paymentStatus) ?>"><?= e($statusLabels[$paymentStatus] ?? $paymentStatus) ?></span></td>
                    <td class="actions">
                        <?php if ($paymentStatus === 'pending'): ?>
                        <form method="post" class="payment-action-form table-actions">
                            <?= csrfField() ?>
                            <input type="hidden" name="payment_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" name="status" value="verified" class="btn btn-primary btn-sm">ยืนยัน</button>
                            <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">ปฏิเสธ</button>
                        </form>
                        <?php elseif ($paymentStatus === 'rejected'): ?>
                        <form method="post" class="payment-action-form table-actions">
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
                <tr class="payment-note-tr">
                    <td colspan="8" class="payment-note-row">
                        <span class="payment-note-label">หมายเหตุ</span>
                        <?= e($p['note']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p class="table-empty">ยังไม่มีรายการ</p>
        <?php endif; ?>
    </div>
</div>

<div class="admin-modal" id="paymentSlipModal" hidden>
    <div class="admin-modal-backdrop" data-close-slip-modal></div>
    <div class="admin-modal-dialog payment-slip-dialog" role="dialog" aria-modal="true" aria-labelledby="paymentSlipModalTitle">
        <button type="button" class="admin-modal-close" data-close-slip-modal aria-label="ปิด">
            <?= lucide_icon('x', ['size' => 18]) ?>
        </button>
        <div class="admin-modal-header">
            <h2 id="paymentSlipModalTitle">สลิปการโอนเงิน</h2>
            <p class="payment-slip-modal-meta" id="paymentSlipModalMeta"></p>
        </div>
        <div class="payment-slip-modal-body">
            <div class="payment-slip-frame" id="paymentSlipFrame">
                <img id="paymentSlipImage" class="payment-slip-preview" alt="สลิปการโอนเงิน" hidden>
                <iframe id="paymentSlipPdf" class="payment-slip-preview payment-slip-preview--pdf" title="สลิปการโอนเงิน" hidden></iframe>
            </div>
        </div>
        <div class="payment-slip-modal-footer">
            <a href="#" id="paymentSlipOpenTab" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">เปิดแท็บใหม่</a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
