<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/cart.php';
require_once dirname(__DIR__) . '/includes/homepage.php';
require_once dirname(__DIR__) . '/includes/instructor.php';
require_once dirname(__DIR__) . '/includes/site_content.php';

$courses = [];
$cartSuccess = flash('cart_success');
$newsletterSuccess = flash('newsletter_success');
$newsletterError = flash('newsletter_error');

try {
    $courses = getCourses();
} catch (Throwable $e) {
    $courses = [];
}

$featuredList = array_values(array_filter($courses, fn($c) => !empty($c['is_featured'])));
$otherList = array_values(array_filter($courses, fn($c) => empty($c['is_featured'])));
$homeCourses = array_slice(array_merge($featuredList, $otherList), 0, 8);
$homeStats = getHomepageStats();
$homeContent = getHomepageContent();
$faqItems = getHomepageFaqItems();
$reviews = getHomeReviews();
$lineUrl = lineContactUrl();
$instructor = getInstructorProfile($homeStats);
$instructorPhotoUrl = instructorPhotoUrl($instructor);
$trustItems = $homeContent['trust'] ?? [];
$resolveTrustValue = static function (array $item) use ($homeStats): string {
    $mode = $item['mode'] ?? 'manual';
    return match ($mode) {
        'students' => number_format(max((int) ($homeStats['students'] ?? 0), 5000)) . '+',
        'courses' => (string) (int) ($homeStats['courses'] ?? 0),
        'lessons' => (int) ($homeStats['lessons'] ?? 0) . '+',
        default => (string) ($item['value'] ?? ''),
    };
};
?>

<?php if ($cartSuccess): ?>
<div class="container" style="padding-top:1rem"><div class="alert alert-success"><?= e($cartSuccess) ?></div></div>
<?php endif; ?>
<?php
$heroImgWidth = 2172;
$heroImgHeight = 724;
$heroMobileSize = 1024;
$heroSlides = [
    [
        'src' => imageAsset('images/hero/hero-1.png', 'images/hero/hero-1.svg'),
        'srcMobile' => imageAsset('images/hero/hero-1-mobile.png', 'images/hero/hero-1.svg'),
        'alt' => 'เหวินซิน ปั้นภาษาจีนให้เป็นเรื่องง่าย — เรียนภาษาจีนกับ Wenxin Chinese',
        'href' => APP_URL . '/public/courses.php',
    ],
    [
        'src' => imageAsset('images/hero/hero-2.png', 'images/hero/hero-2.svg'),
        'srcMobile' => imageAsset('images/hero/hero-2-mobile.png', 'images/hero/hero-2.svg'),
        'alt' => 'เริ่มเรียนภาษาจีนวันนี้ — สมัครเรียนกับ Wenxin Chinese',
        'href' => APP_URL . '/public/courses.php',
    ],
    [
        'src' => imageAsset('images/hero/hero-3.png', 'images/hero/hero-3.svg'),
        'srcMobile' => imageAsset('images/hero/hero-3-mobile.png', 'images/hero/hero-3.svg'),
        'alt' => 'ทำไมต้องเรียนกับ Wenxin Chinese',
        'href' => APP_URL . '/public/courses.php',
    ],
];
?>
<section class="hero hero-slider" id="heroSlider" aria-roledescription="carousel" aria-label="แบนเนอร์หลัก">
    <div class="hero-slider-viewport">
        <div class="hero-slider-track">
            <?php foreach ($heroSlides as $i => $slide): ?>
                <div class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>">
                    <a href="<?= e($slide['href']) ?>" class="hero-slide-link">
                        <picture>
                            <source
                                media="(max-width: 768px)"
                                srcset="<?= e($slide['srcMobile']) ?> <?= (int) $heroMobileSize ?>w"
                            >
                            <img
                                src="<?= e($slide['src']) ?>"
                                srcset="<?= e($slide['src']) ?> <?= (int) $heroImgWidth ?>w"
                                sizes="100vw"
                                alt="<?= e($slide['alt']) ?>"
                                width="<?= (int) $heroImgWidth ?>"
                                height="<?= (int) $heroImgHeight ?>"
                                decoding="async"
                                <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>
                            >
                        </picture>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <button type="button" class="hero-slider-btn hero-slider-prev" aria-label="สไลด์ก่อนหน้า">
        <?= lucide_icon('chevron-left', ['size' => 24, 'stroke' => '2.5']) ?>
    </button>
    <button type="button" class="hero-slider-btn hero-slider-next" aria-label="สไลด์ถัดไป">
        <?= lucide_icon('chevron-right', ['size' => 24, 'stroke' => '2.5']) ?>
    </button>
    <div class="hero-slider-dots" role="tablist" aria-label="เลือกสไลด์"></div>
</section>

<section class="trust-bar" aria-label="สถิติ Wenxin Chinese">
    <div class="container">
        <ul class="trust-bar-grid">
            <?php foreach ($trustItems as $trustItem): ?>
            <li>
                <span class="trust-bar-icon" aria-hidden="true">
                    <?= trustBarIcon($trustItem) ?>
                </span>
                <strong><?= e($resolveTrustValue($trustItem)) ?></strong>
                <span><?= e($trustItem['label'] ?? '') ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="section section-alt why-wenxin-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow"><?= e($homeContent['why']['eyebrow'] ?? '') ?></span>
            <h2><?= e($homeContent['why']['title'] ?? '') ?></h2>
            <p><?= e($homeContent['why']['subtitle'] ?? '') ?></p>
        </div>
        <div class="why-wenxin-grid">
            <?php foreach (($homeContent['why']['cards'] ?? []) as $i => $whyCard): ?>
            <article class="why-card">
                <div class="why-card-icon" aria-hidden="true">
                    <?= whyCardIcon($i) ?>
                </div>
                <h3><?= e($whyCard['title'] ?? '') ?></h3>
                <p><?= e($whyCard['text'] ?? '') ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section home-courses-section" id="courses">
    <div class="container">
        <div class="section-header-row">
            <div class="section-header section-header-left">
                <h2><?= e($homeContent['courses']['title'] ?? 'คอร์สเรียนยอดนิยม') ?></h2>
                <p><?= e($homeContent['courses']['subtitle'] ?? '') ?></p>
            </div>
            <a href="<?= APP_URL ?>/public/courses.php" class="section-link-more">ดูคอร์สทั้งหมด →</a>
        </div>
        <?php if ($homeCourses): ?>
        <div class="courses-grid courses-home-grid">
            <?php foreach ($homeCourses as $course): ?>
                <?php include dirname(__DIR__) . '/includes/course_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="lesson-empty">กำลังโหลดคอร์ส... กรุณา import ฐานข้อมูลจาก database/schema.sql</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt home-instructor-section" id="instructor" aria-labelledby="instructor-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="instructor-heading"><?= e($homeContent['instructor']['title'] ?? 'พบกับอาจารย์เหวินซิน') ?></h2>
            <p><?= e($instructor['quote']) ?></p>
        </div>
        <?php
        $photoUrl = $instructorPhotoUrl;
        $showFullLink = true;
        $showStats = true;
        require dirname(__DIR__) . '/includes/views/instructor_showcase.php';
        ?>
    </div>
</section>

<section class="section home-reviews-section" id="reviews" aria-labelledby="reviews-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="reviews-heading"><?= e($homeContent['reviews']['title'] ?? '') ?></h2>
            <p><?= e($homeContent['reviews']['subtitle'] ?? '') ?></p>
        </div>
        <?php
        $renderReviewCard = static function (array $review): void {
            ?>
            <article class="review-card review-card-grid">
                <div class="review-stars" aria-label="5 จาก 5 ดาว">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                    <?= lucide_icon('star', ['size' => 16, 'class' => 'review-star']) ?>
                    <?php endfor; ?>
                </div>
                <blockquote class="review-quote">&ldquo;<?= e($review['quote']) ?>&rdquo;</blockquote>
                <footer class="review-author">
                    <span class="review-avatar" style="--avatar-hue: <?= (int) $review['hue'] ?>"><?= e($review['initial']) ?></span>
                    <div class="review-author-meta">
                        <strong>คุณ<?= e($review['name']) ?></strong>
                        <span>ผู้เรียน <?= e($review['course']) ?></span>
                    </div>
                </footer>
            </article>
            <?php
        };
        ?>
        <div class="reviews-grid reviews-grid--desktop">
            <?php foreach (array_slice($reviews, 0, 4) as $review): ?>
                <?php $renderReviewCard($review); ?>
            <?php endforeach; ?>
        </div>
        <div class="reviews-slider reviews-slider--mobile" id="reviewsSlider">
            <button type="button" class="reviews-slider-btn reviews-slider-prev" aria-label="รีวิวก่อนหน้า">
                <?= lucide_icon('chevron-left', ['size' => 24, 'stroke' => '2.5']) ?>
            </button>
            <div class="reviews-slider-viewport">
                <div class="reviews-slider-track">
                    <?php foreach ($reviews as $review): ?>
                        <?php $renderReviewCard($review); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="reviews-slider-btn reviews-slider-next" aria-label="รีวิวถัดไป">
                <?= lucide_icon('chevron-right', ['size' => 24, 'stroke' => '2.5']) ?>
            </button>
            <div class="reviews-slider-dots" role="tablist" aria-label="เลือกหน้ารีวิว"></div>
        </div>
    </div>
</section>

<section class="section section-alt home-steps-section">
    <div class="container">
        <div class="section-header">
            <h2><?= e($homeContent['steps']['title'] ?? '') ?></h2>
            <p><?= e($homeContent['steps']['subtitle'] ?? '') ?></p>
        </div>
        <div class="home-steps-flow">
            <?php foreach (($homeContent['steps']['items'] ?? []) as $stepIndex => $step): ?>
            <?php if ($stepIndex > 0): ?><div class="home-step-connector" aria-hidden="true"></div><?php endif; ?>
            <div class="home-step-node">
                <div class="home-step-icon" aria-hidden="true">
                    <?= homeStepIcon((int) $stepIndex) ?>
                </div>
                <h3><?= e($step['title'] ?? '') ?></h3>
                <p><?= e($step['text'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section home-faq-section" id="faq">
    <div class="container">
        <div class="section-header-row">
            <div class="section-header section-header-left">
                <h2><?= e($homeContent['faq']['title'] ?? '') ?></h2>
                <p><?= e($homeContent['faq']['subtitle'] ?? '') ?></p>
            </div>
            <a href="<?= APP_URL ?>/public/faq.php" class="section-link-more">ดูทั้งหมด →</a>
        </div>
        <?php include dirname(__DIR__) . '/includes/faq_panel.php'; ?>
    </div>
</section>

<section class="home-newsletter" id="newsletter">
    <div class="container home-newsletter-inner">
        <div class="home-newsletter-info">
            <div class="home-newsletter-icon" aria-hidden="true">
                <?= lucide_icon('mail', ['size' => 48, 'stroke' => '1.75']) ?>
            </div>
            <div class="home-newsletter-copy">
                <h2><?= e($homeContent['newsletter']['title'] ?? '') ?></h2>
                <p><?= e($homeContent['newsletter']['subtitle'] ?? '') ?></p>
            </div>
        </div>
        <div class="home-newsletter-form-wrap">
            <?php if ($newsletterSuccess): ?>
            <p class="home-newsletter-feedback home-newsletter-feedback-success" role="status"><?= e($newsletterSuccess) ?></p>
            <?php elseif ($newsletterError): ?>
            <p class="home-newsletter-feedback home-newsletter-feedback-error" role="alert"><?= e($newsletterError) ?></p>
            <?php endif; ?>
            <form class="home-newsletter-form" method="post" action="<?= APP_URL ?>/public/newsletter-subscribe.php">
                <?= csrfField() ?>
                <div class="home-newsletter-field">
                    <label class="visually-hidden" for="newsletter-email">อีเมลของคุณ</label>
                    <input type="email" id="newsletter-email" name="email" placeholder="<?= e($homeContent['newsletter']['placeholder'] ?? 'อีเมลของคุณ') ?>" required autocomplete="email">
                    <button type="submit"><?= e($homeContent['newsletter']['button'] ?? 'สมัครรับข่าวสาร') ?></button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
