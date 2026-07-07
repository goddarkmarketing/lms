<?php
declare(strict_types=1);
/** @var array $student */
require_once dirname(__DIR__) . '/line_messaging.php';

$lineOaOn = isLineOaEnabled();
$lineLinked = studentHasLineLinked((int) ($student['id'] ?? 0));
$addFriendUrl = lineOaAddFriendUrl();
$lineBasicId = lineOaBasicId();
$studentPhone = normalizePhoneDigits((string) ($student['phone'] ?? ''));
?>
<div class="account-line-panel">
    <div class="account-line-panel-head">
        <?= brand_icon('line', ['size' => 22, 'class' => 'account-line-icon']) ?>
        <div>
            <strong>แจ้งเตือนผ่าน LINE</strong>
            <p>รับการยืนยันจองคลาส ลิงก์ Zoom และเตือนก่อนเริ่มเรียน</p>
        </div>
    </div>

    <?php if (!$lineOaOn): ?>
    <p class="account-line-note">ระบบ LINE ยังไม่เปิดใช้งาน — ติดต่อทีมงานหากต้องการรับแจ้งเตือน</p>
    <?php elseif ($lineLinked): ?>
    <div class="account-line-status account-line-status--ok">
        <?= lucide_icon('circle-check', ['size' => 18]) ?>
        <span>เชื่อม LINE แล้ว — จะได้รับแจ้งเตือนการจองและคลาสเรียน</span>
    </div>
    <?php else: ?>
    <ol class="account-line-steps">
        <li>
            <?php if ($addFriendUrl): ?>
            <a href="<?= e($addFriendUrl) ?>" target="_blank" rel="noopener" class="btn btn-line btn-sm">Add Friend LINE OA</a>
            <?php if ($lineBasicId !== ''): ?>
            <span class="account-line-id">@<?= e($lineBasicId) ?></span>
            <?php endif; ?>
            <?php else: ?>
            ค้นหา Official Account ของ Wenxin Chinese ใน LINE แล้วกด Add Friend
            <?php endif; ?>
        </li>
        <li>เปิดแชท แล้วส่ง<strong>เบอร์โทรที่ใช้สมัคร</strong><?php if ($studentPhone !== ''): ?> (<?= e($studentPhone) ?>)<?php endif; ?></li>
        <li>รอข้อความตอบกลับ「เชื่อมบัญชีเรียบร้อยแล้ว」</li>
    </ol>
    <?php endif; ?>
</div>
