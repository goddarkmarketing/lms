<?php
declare(strict_types=1);

$pageTitle = 'ตะกร้าของฉัน';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/cart.php';
require_once dirname(__DIR__) . '/includes/coupon.php';
require_once dirname(__DIR__) . '/includes/checkout_flow.php';

$cartSuccess = flash('cart_success');
$cartError = flash('payment_error');
$items = cartItems();
$subtotal = cartSubtotal();
$discount = cartDiscount();
$total = cartTotal();
$appliedCoupon = getAppliedCoupon();
?>

<main class="checkout-page cart-page">
    <div class="container">
        <?php if ($cartSuccess): ?>
        <div class="alert alert-success checkout-alert"><?= e($cartSuccess) ?></div>
        <?php endif; ?>
        <?php if ($cartError): ?>
        <div class="alert alert-error checkout-alert"><?= e($cartError) ?></div>
        <?php endif; ?>

        <?php renderCheckoutSteps(2); ?>

        <div class="cart-page-layout">
            <section class="cart-page-main">
                <header class="checkout-main-header">
                    <span class="checkout-main-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 6h15l-1.5 9h-12z"></path>
                            <path d="M6 6l-2-2"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>
                    </span>
                    <h1>ตะกร้าของฉัน</h1>
                </header>

                <?php if ($items): ?>
                <ul class="cart-page-list">
                    <?php foreach ($items as $item): ?>
                    <li class="cart-page-item">
                        <img src="<?= e(courseCoverUrl($item)) ?>" alt="" class="checkout-order-thumb" width="72" height="72" loading="lazy">
                        <div class="cart-page-item-body">
                            <h2><a href="<?= APP_URL ?>/public/course.php?slug=<?= urlencode($item['slug']) ?>"><?= e($item['title']) ?></a></h2>
                            <p class="cart-page-item-price"><?= e(formatPrice((float) ($item['price'] ?? 0))) ?></p>
                        </div>
                        <a class="cart-page-remove" href="<?= APP_URL ?>/public/cart_remove.php?course_id=<?= (int) $item['id'] ?>&return=<?= urlencode('/public/cart.php') ?>">ลบ</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <form method="post" action="<?= APP_URL ?>/public/apply_coupon.php" class="cart-coupon-form">
                    <?= csrfField() ?>
                    <label for="coupon_code">รหัสส่วนลด</label>
                    <div class="cart-coupon-row">
                        <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="เช่น WENXIN10" value="<?= e($appliedCoupon['code'] ?? '') ?>">
                        <?php if ($appliedCoupon): ?>
                        <button type="submit" name="remove_coupon" value="1" class="btn btn-outline btn-sm">ลบ</button>
                        <?php else: ?>
                        <button type="submit" class="btn btn-outline btn-sm">ใช้รหัส</button>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="cart-page-total">
                    <span>ยอดรวม</span>
                    <strong><?= e(formatPrice($subtotal > 0 ? $subtotal : null)) ?></strong>
                </div>
                <?php if ($discount > 0): ?>
                <div class="cart-page-total cart-page-discount">
                    <span>ส่วนลด</span>
                    <strong>-<?= e(formatPrice($discount)) ?></strong>
                </div>
                <?php endif; ?>
                <div class="cart-page-total cart-page-payable">
                    <span>ยอดชำระ</span>
                    <strong><?= e(formatPrice($total > 0 ? $total : null)) ?></strong>
                </div>
                <div class="cart-page-actions">
                    <a href="<?= APP_URL ?>/public/courses.php" class="btn btn-outline">เลือกคอร์สเพิ่ม</a>
                    <a href="<?= APP_URL ?>/public/checkout.php" class="btn btn-primary">ไปชำระเงิน</a>
                </div>
                <?php else: ?>
                <div class="cart-page-empty">
                    <p>ยังไม่มีคอร์สในตะกร้า</p>
                    <a href="<?= APP_URL ?>/public/courses.php" class="btn btn-primary">เลือกคอร์สเรียน</a>
                </div>
                <?php endif; ?>
            </section>

            <aside class="checkout-sidebar">
                <div class="checkout-trust-card">
                    <h3>ขั้นตอนถัดไป</h3>
                    <ul>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path></svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>1. เลือกคอร์ส</strong>
                                <span>เลือกคอร์สที่เหมาะกับระดับของคุณ</span>
                            </div>
                        </li>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6l-2-2"></path></svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>2. ใส่ตะกร้า</strong>
                                <span>ตรวจสอบคอร์สในตะกร้าก่อนชำระเงิน</span>
                            </div>
                        </li>
                        <li>
                            <span class="checkout-mini-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
                            </span>
                            <div class="checkout-mini-text">
                                <strong>3. ชำระเงิน</strong>
                                <span>โอนเงินและแนบสลิปยืนยัน</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
