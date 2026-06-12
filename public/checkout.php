<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/cart.php';
require_once dirname(__DIR__) . '/includes/checkout_flow.php';
require_once dirname(__DIR__) . '/includes/student_auth.php';
require_once dirname(__DIR__) . '/includes/coupon.php';
require_once dirname(__DIR__) . '/includes/promptpay.php';
require_once dirname(__DIR__) . '/includes/omise.php';

$paymentSuccess = flash('payment_success');
$paymentError = flash('payment_error');
$checkoutStudent = currentStudent();

if (!$paymentSuccess) {
    $courseSlug = trim($_GET['course'] ?? '');
    if ($courseSlug !== '') {
        if (!addCourseSlugToCart($courseSlug)) {
            flash('payment_error', 'ไม่พบคอร์สที่เลือก');
            redirect('/public/courses.php');
        }
        redirect('/public/cart.php');
    }
    requireCartForCheckout();
}

$pageTitle = 'ชำระเงิน';
require_once dirname(__DIR__) . '/includes/header.php';

$orderItems = cartItems();
$preselectAmount = (string) (cartTotal() ?: '');
$cartNotePrefill = cartCount() > 0 ? 'คอร์สในตะกร้า: ' . cartTitlesSummary() : '';
$preselectCourseId = count($orderItems) === 1 ? (int) ($orderItems[0]['id'] ?? 0) : 0;
$orderTotal = cartTotal();
$orderSubtotal = cartSubtotal();
$orderDiscount = cartDiscount();
$bankNumber = getSetting('bank_account_number');
$bankName = getSetting('bank_name');
$bankAccountName = getSetting('bank_account_name');
$bankDigits = preg_replace('/\D/', '', $bankNumber);
$bankNumberDisplay = strlen($bankDigits) === 10
    ? substr($bankDigits, 0, 3) . '-' . substr($bankDigits, 3, 1) . '-' . substr($bankDigits, 4, 5) . '-' . substr($bankDigits, 9, 1)
    : $bankNumber;
$checkoutStep = $paymentSuccess ? 4 : 3;
$promptPay = getCheckoutPromptPayData($orderTotal);
$omiseEnabled = isOmiseEnabled();
$omisePublicKey = omisePublicKey();
?>

<main class="checkout-page">
    <div class="container">
        <?php if ($paymentSuccess): ?>
        <div class="alert alert-success checkout-alert"><?= e($paymentSuccess) ?></div>
        <?php endif; ?>
        <?php if ($paymentError): ?>
        <div class="alert alert-error checkout-alert"><?= e($paymentError) ?></div>
        <?php endif; ?>

        <?php renderCheckoutSteps($checkoutStep); ?>

        <?php if ($paymentSuccess): ?>
        <div class="checkout-complete-card">
            <h2>แจ้งชำระเงินเรียบร้อยแล้ว</h2>
            <p>ทีมงานจะตรวจสอบและเปิดสิทธิ์เรียนภายใน 24 ชั่วโมง เมื่อเปิดสิทธิ์แล้วสามารถเริ่มเรียนได้ทันที</p>
            <div class="checkout-complete-actions">
                <a href="<?= APP_URL ?>/public/my-courses.php" class="btn btn-primary">ไปหน้าเริ่มเรียน</a>
                <a href="<?= APP_URL ?>/public/courses.php" class="btn btn-outline">เลือกคอร์สเพิ่ม</a>
            </div>
        </div>
        <?php else: ?>
        <div class="checkout-layout">
            <div class="checkout-main">
                <header class="checkout-main-header">
                    <span class="checkout-main-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                            <path d="M2 10h20"></path>
                        </svg>
                    </span>
                    <h1>ชำระเงินซื้อคอร์สเรียน</h1>
                </header>

                <section class="checkout-panel">
                    <div class="checkout-method is-active">
                        <div class="checkout-method-head">
                            <span class="checkout-method-radio" aria-hidden="true"></span>
                            <div>
                                <h2>ชำระเงินโดยการโอนเงินผ่านธนาคาร</h2>
                                <p>โอนเงินตามยอดด้านล่าง แล้วแนบสลิปเพื่อยืนยันการชำระเงิน</p>
                            </div>
                        </div>

                        <div class="checkout-transfer-panel">
                            <div class="checkout-bank-card<?= $promptPay ? ' checkout-bank-card--with-qr' : '' ?>">
                                <div class="checkout-bank-card-body">
                                    <div class="checkout-bank-card-info">
                                        <div class="checkout-bank-card-top">
                                            <img src="<?= e(imageAsset('images/checkout/kbank-logo.png', 'images/checkout/kbank-logo.svg')) ?>" alt="โลโก้<?= e($bankName) ?>" class="checkout-bank-logo" width="52" height="52" loading="lazy" decoding="async">
                                            <div class="checkout-bank-card-brand">
                                                <span class="checkout-bank-card-bank"><?= e($bankName) ?></span>
                                            </div>
                                        </div>
                                        <dl class="checkout-bank-details">
                                            <div class="checkout-bank-detail-row">
                                                <dt>ชื่อบัญชี</dt>
                                                <dd><?= e($bankAccountName) ?></dd>
                                            </div>
                                            <div class="checkout-bank-detail-row checkout-bank-detail-row--account">
                                                <dt>เลขที่บัญชี</dt>
                                                <dd>
                                                    <span class="checkout-bank-number" id="checkoutBankNumber"><?= e($bankNumberDisplay) ?></span>
                                                    <button type="button" class="checkout-bank-copy js-copy-bank" data-copy="<?= e($bankDigits ?: $bankNumber) ?>" aria-label="คัดลอกเลขบัญชี">
                                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                        <span class="checkout-bank-copy-label">คัดลอก</span>
                                                    </button>
                                                </dd>
                                            </div>
                                        </dl>
                                        <p class="checkout-bank-note">โอนจากแอป <?= e($bankName) ?> หรือธนาคารอื่นได้ทันที</p>
                                    </div>

                                    <?php if ($promptPay): ?>
                                    <div class="checkout-bank-qr">
                                        <div class="checkout-bank-qr-wrap">
                                            <img src="<?= e($promptPay['qr_url']) ?>" alt="PromptPay QR ยอด <?= e(formatPrice($orderTotal)) ?>" width="200" height="200" class="checkout-bank-qr-img" loading="lazy">
                                        </div>
                                        <p class="checkout-bank-qr-hint">เปิดแอปธนาคาร → สแกน QR → โอนตามยอด</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <ol class="checkout-instructions">
                            <li>โอนเงินตามยอดรวมในสรุปคำสั่งซื้อ</li>
                            <li>เก็บหลักฐานการโอน (สลิป) ไว้</li>
                            <li>กรอกข้อมูลและแนบสลิปด้านล่าง</li>
                            <li>รอทีมงานตรวจสอบและเปิดสิทธิ์เรียน (ภายใน 24 ชม.)</li>
                        </ol>

                        <?php if (getSetting('payment_note')): ?>
                        <p class="checkout-note"><?= e(getSetting('payment_note')) ?></p>
                        <?php endif; ?>

                        <form action="<?= APP_URL ?>/public/payment.php" method="post" enctype="multipart/form-data" class="checkout-form" id="checkoutForm">
                            <?= csrfField() ?>
                            <div class="form-row checkout-form-row">
                                <div class="form-group">
                                    <label for="student_name">ชื่อ-นามสกุล *</label>
                                    <input type="text" id="student_name" name="student_name" class="form-control" required value="<?= e($checkoutStudent['full_name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="student_phone">เบอร์โทร *</label>
                                    <input type="tel" id="student_phone" name="student_phone" class="form-control" required value="<?= e($checkoutStudent['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="student_email">อีเมล</label>
                                <input type="email" id="student_email" name="student_email" class="form-control" placeholder="สำหรับรับการยืนยัน" value="<?= e($checkoutStudent['email'] ?? '') ?>">
                            </div>

                            <input type="hidden" name="from_cart" value="1">
                            <?php if ($preselectCourseId > 0): ?>
                            <input type="hidden" name="course_id" value="<?= $preselectCourseId ?>">
                            <?php endif; ?>

                            <div class="form-row checkout-form-row">
                                <div class="form-group">
                                    <label>จำนวนเงิน (บาท)</label>
                                    <div class="form-control checkout-readonly" id="checkoutTotalDisplay"><?= e(formatPrice($orderTotal > 0 ? $orderTotal : null)) ?></div>
                                </div>
                                <div class="form-group">
                                    <label for="transfer_date">วันที่โอน</label>
                                    <input type="date" id="transfer_date" name="transfer_date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="transfer_time">เวลาโอน</label>
                                <input type="text" id="transfer_time" name="transfer_time" class="form-control" placeholder="เช่น 14:30">
                            </div>

                            <div class="form-group">
                                <label for="slip_image">แนบสลิปการโอนเงิน</label>
                                <div class="checkout-slip-drop" id="slipDropZone">
                                    <input type="file" id="slip_image" name="slip_image" accept="image/*,.pdf" class="checkout-slip-input">
                                    <div class="checkout-slip-placeholder">
                                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        <p><strong>คลิกหรือลากไฟล์</strong> มาวางที่นี่</p>
                                        <span>รองรับ JPG, PNG, PDF (ไม่เกิน 5MB)</span>
                                    </div>
                                    <p class="checkout-slip-filename" id="slipFileName" hidden></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">หมายเหตุเพิ่มเติม</label>
                                <textarea id="note" name="note" class="form-control" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"><?= e($cartNotePrefill) ?></textarea>
                            </div>

                            <div class="checkout-secure-note">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span>ข้อมูลของคุณปลอดภัย ทีมงาน Wenxin จะตรวจสอบและติดต่อกลับหลังได้รับหลักฐานการชำระเงิน</span>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block checkout-submit">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                ยืนยันการชำระเงิน
                            </button>
                        </form>
                    </div>
                </section>

                <?php if ($omiseEnabled): ?>
                <section class="checkout-panel" style="margin-top:1.5rem">
                    <div class="checkout-method is-active">
                        <div class="checkout-method-head">
                            <span class="checkout-method-radio" aria-hidden="true"></span>
                            <div>
                                <h2>ชำระเงินออนไลน์ทันที</h2>
                                <p>PromptPay หรือบัตรเครดิต/เดบิต — เปิดสิทธิ์เรียนอัตโนมัติทันทีที่ชำระสำเร็จ</p>
                            </div>
                        </div>

                        <form action="<?= APP_URL ?>/public/omise_pay.php" method="post" class="checkout-form" id="omiseCheckoutForm">
                            <?= csrfField() ?>
                            <div class="form-row checkout-form-row">
                                <div class="form-group">
                                    <label>ชื่อ-นามสกุล *</label>
                                    <input type="text" name="student_name" class="form-control" required value="<?= e($checkoutStudent['full_name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>เบอร์โทร *</label>
                                    <input type="tel" name="student_phone" class="form-control" required value="<?= e($checkoutStudent['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>อีเมล</label>
                                <input type="email" name="student_email" class="form-control" value="<?= e($checkoutStudent['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>วิธีชำระ</label>
                                <select name="omise_method" class="form-control" id="omiseMethod">
                                    <option value="promptpay">PromptPay (สแกน QR)</option>
                                    <option value="card">บัตรเครดิต / เดบิต</option>
                                </select>
                            </div>
                            <div id="omiseCardFields" hidden>
                                <div class="form-group">
                                    <label>หมายเลขบัตร</label>
                                    <input type="text" id="omiseCardNumber" class="form-control" inputmode="numeric" autocomplete="cc-number" placeholder="4111 1111 1111 1111">
                                </div>
                                <div class="form-row checkout-form-row">
                                    <div class="form-group">
                                        <label>เดือน (MM)</label>
                                        <input type="text" id="omiseCardMonth" class="form-control" maxlength="2" placeholder="12">
                                    </div>
                                    <div class="form-group">
                                        <label>ปี (YYYY)</label>
                                        <input type="text" id="omiseCardYear" class="form-control" maxlength="4" placeholder="2028">
                                    </div>
                                    <div class="form-group">
                                        <label>CVV</label>
                                        <input type="text" id="omiseCardCvv" class="form-control" maxlength="4" autocomplete="cc-csc" placeholder="123">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>ชื่อบนบัตร</label>
                                    <input type="text" id="omiseCardName" class="form-control" autocomplete="cc-name" placeholder="NAME ON CARD">
                                </div>
                            </div>
                            <input type="hidden" name="omise_token" id="omiseToken" value="">
                            <p class="checkout-summary-note" style="margin:1rem 0">ยอดชำระ <strong><?= e(formatPrice($orderTotal)) ?></strong></p>
                            <button type="submit" class="btn btn-primary btn-block checkout-submit" id="omiseSubmitBtn">
                                ชำระเงินออนไลน์ <?= e(formatPrice($orderTotal)) ?>
                            </button>
                        </form>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <aside class="checkout-sidebar">
                <div class="checkout-summary-card">
                    <h2>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        สรุปคำสั่งซื้อ
                    </h2>

                    <?php if ($orderItems): ?>
                    <ul class="checkout-order-list">
                        <?php foreach ($orderItems as $item): ?>
                        <li class="checkout-order-item">
                            <img src="<?= e(courseCoverUrl($item)) ?>" alt="" class="checkout-order-thumb" width="56" height="56" loading="lazy">
                            <div class="checkout-order-meta">
                                <strong><?= e($item['title']) ?></strong>
                                <span><?= e(formatPrice((float) ($item['price'] ?? 0))) ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="checkout-order-empty">ยังไม่ได้เลือกคอร์ส — <a href="<?= APP_URL ?>/public/courses.php">เลือกคอร์สเรียน</a></p>
                    <?php endif; ?>

                    <?php if ($orderDiscount > 0): ?>
                    <dl class="checkout-total-row checkout-discount-row">
                        <dt>ส่วนลด</dt>
                        <dd>-<?= e(formatPrice($orderDiscount)) ?></dd>
                    </dl>
                    <?php endif; ?>
                    <dl class="checkout-total-row">
                        <dt>ยอดชำระทั้งหมด</dt>
                        <dd id="checkoutTotalDisplay"><?= e(formatPrice($orderTotal > 0 ? $orderTotal : null)) ?></dd>
                    </dl>

                    <p class="checkout-summary-note">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        หลังชำระเงิน ทีมงานจะเปิดสิทธิ์เรียนและติดต่อกลับทางอีเมลหรือ Line
                    </p>
                </div>

                <div class="checkout-trust-card">
                    <h3>ทำไมต้องชำระกับ Wenxin</h3>
                    <ul>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>ปลอดภัย มั่นใจ</strong>
                                <span>ตรวจสอบสลิปและเปิดคอร์สอย่างเป็นระบบ</span>
                            </div>
                        </li>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>รวดเร็ว</strong>
                                <span>ยืนยันภายใน 24 ชั่วโมงทำการ</span>
                            </div>
                        </li>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path>
                                    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path>
                                </svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>เรียนได้ทันที</strong>
                                <span>เข้าถึงวิดีโอและเอกสารหลังเปิดสิทธิ์</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="checkout-help-card">
                    <h3>ต้องการความช่วยเหลือ?</h3>
                    <?php if (getSetting('phone')): ?>
                    <a href="tel:<?= e(preg_replace('/\D+/', '', getSetting('phone'))) ?>" class="checkout-help-item">
                        <span class="checkout-mini-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </span>
                        <div class="checkout-mini-text">
                            <strong><?= e(getSetting('phone')) ?></strong>
                            <span>จันทร์–ศุกร์ 9:00–18:00 น.</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    <?php if (getSetting('line_id')): ?>
                    <div class="checkout-help-item">
                        <span class="checkout-mini-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </span>
                        <div class="checkout-mini-text">
                            <strong>Line <?= e(getSetting('line_id')) ?></strong>
                            <span>ตอบกลับภายใน 1 ชั่วโมง</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (getSetting('facebook_url')): ?>
                    <a href="<?= e(getSetting('facebook_url')) ?>" target="_blank" rel="noopener" class="checkout-help-item">
                        <span class="checkout-mini-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </span>
                        <div class="checkout-mini-text">
                            <strong>Facebook</strong>
                            <span>ติดต่อทีมงาน Wenxin</span>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php if ($omiseEnabled && !$paymentSuccess): ?>
<script src="https://cdn.omise.co/omise.js"></script>
<script>
(function () {
    var form = document.getElementById('omiseCheckoutForm');
    if (!form || typeof Omise === 'undefined') return;
    Omise.setPublicKey(<?= json_encode($omisePublicKey) ?>);
    var method = document.getElementById('omiseMethod');
    var cardBox = document.getElementById('omiseCardFields');
    function toggleCard() {
        var show = method && method.value === 'card';
        if (cardBox) cardBox.hidden = !show;
    }
    if (method) method.addEventListener('change', toggleCard);
    toggleCard();
    form.addEventListener('submit', function (e) {
        if (!method || method.value !== 'card') return;
        e.preventDefault();
        var btn = document.getElementById('omiseSubmitBtn');
        if (btn) btn.disabled = true;
        Omise.createToken('card', {
            name: document.getElementById('omiseCardName').value,
            number: document.getElementById('omiseCardNumber').value.replace(/\s+/g, ''),
            expiration_month: parseInt(document.getElementById('omiseCardMonth').value, 10),
            expiration_year: parseInt(document.getElementById('omiseCardYear').value, 10),
            security_code: document.getElementById('omiseCardCvv').value
        }, function (status, response) {
            if (response.id) {
                document.getElementById('omiseToken').value = response.id;
                form.submit();
            } else {
                alert((response.message || 'ไม่สามารถชำระด้วยบัตรได้'));
                if (btn) btn.disabled = false;
            }
        });
    });
})();
</script>
<?php endif; ?>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
