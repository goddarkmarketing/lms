<?php

declare(strict_types=1);

if (!function_exists('lineContactUrl')) {

    require_once __DIR__ . '/homepage.php';

}

$footerLineUrl = lineContactUrl();

$footerHskCourses = getFooterHskCourseLinks();

$footerCheckoutUrl = $checkoutNavUrl ?? APP_URL . '/public/cart.php';

$footerFacebook = trim(getSetting('facebook_url', ''));

$footerLineId = trim(getSetting('line_id', ''));

$footerPhone = trim(getSetting('phone', ''));

$footerEmail = trim(getSetting('email_admin', ''));

$footerYoutube = trim(getSetting('youtube_url', ''));

$footerTiktok = trim(getSetting('tiktok_url', ''));

$footerTagline = getSetting('site_tagline', 'สถาบันสอนภาษาจีนออนไลน์ คุณภาพ เรียนง่าย ได้ผลจริง');

require __DIR__ . '/views/contact_fab.php';

?>

</main>

<footer class="site-footer" id="contact">

    <div class="container footer-grid">

        <div class="footer-brand">
            <a href="<?= APP_URL ?>/public/index.php" class="footer-brand-link">
                <img src="<?= e(brandLogoAsset()) ?>" alt="Wenxin Chinese" class="footer-logo">
                <span class="footer-brand-name">WENXIN CHINESE</span>
            </a>
            <p class="footer-desc"><?= e($footerTagline) ?></p>
            <div class="footer-social">
                <?php if ($footerFacebook !== ''): ?>
                <a href="<?= e($footerFacebook) ?>" class="footer-social-link footer-social-fb" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.027 4.388 11.02 10.125 11.926v-8.43H7.078v-3.496h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.496h-2.796v8.43C19.612 23.093 24 18.1 24 12.073z"></path></svg>
                </a>
                <?php endif; ?>
                <?php if ($footerLineId !== ''): ?>
                <a href="<?= e($footerLineUrl) ?>" class="footer-social-link footer-social-line" target="_blank" rel="noopener" aria-label="Line">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>
                </a>
                <?php endif; ?>
                <?php if ($footerPhone !== ''): ?>
                <a href="tel:<?= e(preg_replace('/\s+/', '', $footerPhone)) ?>" class="footer-social-link footer-social-phone" aria-label="โทรศัพท์">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.56.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.56 1 1 0 0 1-.25 1.01l-2.2 2.22z"></path></svg>
                </a>
                <?php endif; ?>
                <?php if ($footerEmail !== ''): ?>
                <a href="mailto:<?= e($footerEmail) ?>" class="footer-social-link footer-social-email" aria-label="อีเมล">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5L4 8V6l8 5 8-5v2z"></path></svg>
                </a>
                <?php endif; ?>
                <a href="<?= $footerTiktok !== '' ? e($footerTiktok) : '#' ?>" class="footer-social-link footer-social-tiktok"<?= $footerTiktok !== '' ? ' target="_blank" rel="noopener"' : '' ?> aria-label="TikTok">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15.2a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.75a8.18 8.18 0 0 0 4.76 1.52V6.82a4.85 4.85 0 0 1-1-.13z"></path></svg>
                </a>
                <?php if ($footerYoutube !== ''): ?>
                <a href="<?= e($footerYoutube) ?>" class="footer-social-link footer-social-youtube" target="_blank" rel="noopener" aria-label="YouTube">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.5 31.5 0 0 0 24 12a31.5 31.5 0 0 0-.5-5.8zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"></path></svg>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-col">

            <h4>คอร์สเรียน</h4>

            <ul>

                <?php foreach ($footerHskCourses as $fc): ?>

                <li><a href="<?= e($fc['url']) ?>"><?= e($fc['title']) ?></a></li>

                <?php endforeach; ?>

                <?php if (!$footerHskCourses): ?>

                <li><a href="<?= APP_URL ?>/public/courses.php">ดูคอร์สทั้งหมด</a></li>

                <?php endif; ?>

            </ul>

        </div>

        <div class="footer-col">

            <h4>เกี่ยวกับเรา</h4>

            <ul>

                <li><a href="<?= APP_URL ?>/public/index.php">เกี่ยวกับเรา</a></li>

                <li><a href="<?= APP_URL ?>/public/instructor.php">อาจารย์ผู้สอน</a></li>

                <li><a href="<?= APP_URL ?>/public/index.php#reviews">รีวิวจากผู้เรียน</a></li>

                <li><a href="<?= APP_URL ?>/public/courses.php">บทความ</a></li>

            </ul>

        </div>

        <div class="footer-col">

            <h4>ช่วยเหลือ</h4>

            <ul>

                <li><a href="<?= APP_URL ?>/public/faq.php">คำถามที่พบบ่อย</a></li>

                <li><a href="<?= APP_URL ?>/public/register.php">วิธีการสมัครเรียน</a></li>

                <li><a href="<?= e($footerCheckoutUrl) ?>">แจ้งชำระเงิน</a></li>

                <li><a href="<?= APP_URL ?>/public/index.php#contact">ติดต่อเรา</a></li>

            </ul>

        </div>

        <div class="footer-col footer-contact-col">

            <h4>ติดต่อเรา</h4>

            <ul class="footer-contact-plain">

                <?php if ($footerLineId !== ''): ?>

                <li>LINE: <a href="<?= e($footerLineUrl) ?>" target="_blank" rel="noopener"><?= e($footerLineId) ?></a></li>

                <?php endif; ?>

                <?php if ($footerPhone !== ''): ?>

                <li>โทร: <a href="tel:<?= e(preg_replace('/\s+/', '', $footerPhone)) ?>"><?= e($footerPhone) ?></a></li>

                <?php endif; ?>

                <?php if ($footerEmail !== ''): ?>

                <li>อีเมล: <a href="mailto:<?= e($footerEmail) ?>"><?= e($footerEmail) ?></a></li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

    <div class="footer-bottom">

        <div class="container footer-bottom-inner">

            <p>&copy; <?= date('Y') ?> Wenxin Chinese. All Rights Reserved.</p>

            <div class="footer-bottom-links">

                <a href="<?= APP_URL ?>/public/privacy.php">นโยบายความเป็นส่วนตัว</a>

                <span class="footer-bottom-sep" aria-hidden="true">|</span>

                <a href="<?= APP_URL ?>/public/terms.php">เงื่อนไขการให้บริการ</a>

            </div>

        </div>

    </div>

</footer>

<?php

$toastMessage = $cartSuccess ?? null;

if (!empty($toastMessage)):

?>

<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true">

    <div class="toast toast-success" data-toast role="status">

        <span class="toast-icon" aria-hidden="true">

            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">

                <path d="M20 6L9 17l-5-5"></path>

            </svg>

        </span>

        <span class="toast-text"><?= e($toastMessage) ?></span>

    </div>

</div>

<?php endif; ?>

<script src="<?= asset('js/main.js') ?>"></script>

</body>

</html>


