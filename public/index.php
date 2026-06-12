<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/cart.php';
require_once dirname(__DIR__) . '/includes/homepage.php';
require_once dirname(__DIR__) . '/includes/instructor.php';

$courses = [];
$cartSuccess = flash('cart_success');
$newsletterSuccess = flash('newsletter_success');
$newsletterError = flash('newsletter_error');

try {
    $courses = getCourses();
} catch (Throwable $e) {
    $courses = [];
}

$featured = array_filter($courses, fn($c) => !empty($c['is_featured']));
$displayCourses = $featured ?: array_slice($courses, 0, 6);
$sliderCourses = array_values($displayCourses);
$homeStats = getHomepageStats();
$faqItems = getHomepageFaqItems();
$lineUrl = lineContactUrl();
$instructor = getInstructorProfile($homeStats);
$instructorPhotoUrl = instructorPhotoUrl($instructor);
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
$reviews = [
    ['quote' => 'เรียนเข้าใจง่าย อธิบายละเอียด มีแบบฝึกหัดให้ทำเยอะมาก สอบผ่าน HSK 3 ได้จริงค่ะ!', 'name' => 'แพรวา', 'course' => 'HSK 3', 'initial' => 'พ', 'hue' => 350],
    ['quote' => 'อาจารย์สอนดีมาก เป็นกันเอง เนื้อหาครบ ใช้เรียนต่อยอด และทำงานได้จริง', 'name' => 'กร', 'course' => 'HSK 4', 'initial' => 'ก', 'hue' => 25],
    ['quote' => 'ชอบที่เรียนซ้ำได้ไม่จำกัดเวลา สะดวกมาก เรียนได้ทุกที่เลยค่ะ', 'name' => 'นัท', 'course' => 'HSK 2', 'initial' => 'น', 'hue' => 200],
    ['quote' => 'คอร์สพื้นฐานช่วยให้เริ่มจากศูนย์ เข้าใจง่ายมาก แนะนำเลย!', 'name' => 'บี', 'course' => 'ภาษาจีนพื้นฐาน', 'initial' => 'บ', 'hue' => 140],
    ['quote' => 'พินอินอ่านออกเขียนได้ในไม่กี่สัปดาห์ วิธีสอนชัดเจน เหมาะกับมือใหม่มาก', 'name' => 'มิ้น', 'course' => 'HSK 1', 'initial' => 'ม', 'hue' => 310],
    ['quote' => 'คอร์สติวสอบช่วยให้มั่นใจขึ้นเยอะ โฟกัสข้อสอบจริง สอบ HSK 5 ผ่านในครั้งแรก', 'name' => 'โอ๋', 'course' => 'HSK 5', 'initial' => 'โ', 'hue' => 15],
    ['quote' => 'เอกสารประกอบครบ ทบทวนก่อนสอบได้สะดวก อธิบาย grammar ละเอียดมาก', 'name' => 'เจ', 'course' => 'ติวสอบ HSK 4', 'initial' => 'จ', 'hue' => 260],
    ['quote' => 'เคยเรียนที่อื่นมาแล้ว แต่ที่นี่เข้าใจง่ายกว่า การบ้านและแบบฝึกหัดช่วยได้จริง', 'name' => 'ฟ้า', 'course' => 'HSK 3', 'initial' => 'ฟ', 'hue' => 190],
    ['quote' => 'สอนเป็นขั้นตอน ไม่รีบ ไม่งง เริ่มจากไม่รู้อะไรเลยตอนนี้พูดได้แล้ว', 'name' => 'ต้', 'course' => 'ภาษาจีนพื้นฐาน', 'initial' => 'ต', 'hue' => 45],
    ['quote' => 'ราคาคุ้มค่า เนื้อหาเยอะ ดูย้อนหลังกี่รอบก็ได้ แนะนำเพื่อนมาเรียนแล้ว', 'name' => 'ปั้น', 'course' => 'HSK 2', 'initial' => 'ป', 'hue' => 330],
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
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
    </button>
    <button type="button" class="hero-slider-btn hero-slider-next" aria-label="สไลด์ถัดไป">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
    </button>
    <div class="hero-slider-dots" role="tablist" aria-label="เลือกสไลด์"></div>
</section>

<section class="trust-bar" aria-label="สถิติ Wenxin Chinese">
    <div class="container">
        <ul class="trust-bar-grid">
            <li>
                <span class="trust-bar-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
                <strong><?= number_format(max($homeStats['students'], 5000)) ?>+</strong>
                <span>ผู้เรียนทั่วประเทศ</span>
            </li>
            <li>
                <span class="trust-bar-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path></svg>
                </span>
                <strong><?= (int) $homeStats['courses'] ?></strong>
                <span>คอร์สออนไลน์</span>
            </li>
            <li>
                <span class="trust-bar-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>
                </span>
                <strong><?= (int) $homeStats['lessons'] ?>+</strong>
                <span>บทเรียนวิดีโอ</span>
            </li>
            <li>
                <span class="trust-bar-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                </span>
                <strong>HSK 1–5</strong>
                <span>ครบทุกระดับ</span>
            </li>
        </ul>
    </div>
</section>

<section class="section section-alt why-wenxin-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">จุดเด่นของเรา</span>
            <h2>ทำไมต้องเรียนกับ Wenxin</h2>
            <p>สอนภาษาจีนอย่างเป็นระบบ เน้นพื้นฐานแน่น พร้อมเตรียมสอบ HSK</p>
        </div>
        <div class="why-wenxin-grid">
            <article class="why-card">
                <div class="why-card-icon" aria-hidden="true">
                    <img src="<?= e(iconAsset('Layer 2.png')) ?>" alt="" height="40" loading="lazy">
                </div>
                <h3>อาจารย์ผู้เชี่ยวชาญ</h3>
                <p>สอนโดยเหล่าซือเหวินซิน ปริญญาโทการสอนภาษาจีน มหาวิทยาลัยปักกิ่ง</p>
            </article>
            <article class="why-card">
                <div class="why-card-icon" aria-hidden="true">
                    <img src="<?= e(iconAsset('Layer 3.png')) ?>" alt="" height="40" loading="lazy">
                </div>
                <h3>หลักสูตรมาตรฐาน</h3>
                <p>ครบตั้งแต่พินอิน HSK 1 ถึง HSK 5 และคอร์สติวสอบเฉพาะทาง</p>
            </article>
            <article class="why-card">
                <div class="why-card-icon" aria-hidden="true">
                    <img src="<?= e(iconAsset('Layer 4.png')) ?>" alt="" height="40" loading="lazy">
                </div>
                <h3>เรียนได้ทุกที่</h3>
                <p>วิดีโอและเอกสารครบ ดูย้อนหลังได้ไม่จำกัดบนมือถือและคอมพิวเตอร์</p>
            </article>
            <article class="why-card">
                <div class="why-card-icon" aria-hidden="true">
                    <img src="<?= e(iconAsset('Layer 5.png')) ?>" alt="" height="40" loading="lazy">
                </div>
                <h3>ดูแลใกล้ชิด</h3>
                <p>ทีมงานพร้อมช่วยเหลือผ่าน Line และ Facebook ตลอดการเรียน</p>
            </article>
        </div>
    </div>
</section>

<section class="section home-courses-section" id="courses">
    <div class="container">
        <div class="section-header-row">
            <div class="section-header section-header-left">
                <h2>คอร์สเรียนยอดนิยม</h2>
                <p>เลือกคอร์สที่เหมาะกับระดับและเป้าหมายของคุณ</p>
            </div>
            <a href="<?= APP_URL ?>/public/courses.php" class="section-link-more">ดูคอร์สทั้งหมด →</a>
        </div>
        <?php if ($sliderCourses): ?>
        <div class="courses-slider" id="coursesSlider">
            <button type="button" class="courses-slider-btn courses-slider-prev" aria-label="คอร์สก่อนหน้า">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <div class="courses-slider-viewport">
                <div class="courses-slider-track">
                    <?php foreach ($sliderCourses as $course): ?>
                        <?php include dirname(__DIR__) . '/includes/course_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="courses-slider-btn courses-slider-next" aria-label="คอร์สถัดไป">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
            </button>
            <div class="courses-slider-dots" role="tablist" aria-label="เลือกหน้าคอร์ส"></div>
        </div>
        <?php else: ?>
        <p class="lesson-empty">กำลังโหลดคอร์ส... กรุณา import ฐานข้อมูลจาก database/schema.sql</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt home-instructor-section" id="instructor" aria-labelledby="instructor-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="instructor-heading">พบกับอาจารย์เหวินซิน</h2>
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
            <h2 id="reviews-heading">ความสำเร็จของผู้เรียน</h2>
            <p>เสียงจากผู้เรียนที่ผ่านคอร์สกับ Wenxin Chinese</p>
        </div>
        <?php
        $renderReviewCard = static function (array $review): void {
            ?>
            <article class="review-card review-card-grid">
                <div class="review-stars" aria-label="5 จาก 5 ดาว">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                    <svg class="review-star" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
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
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <div class="reviews-slider-viewport">
                <div class="reviews-slider-track">
                    <?php foreach ($reviews as $review): ?>
                        <?php $renderReviewCard($review); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="reviews-slider-btn reviews-slider-next" aria-label="รีวิวถัดไป">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>
            </button>
            <div class="reviews-slider-dots" role="tablist" aria-label="เลือกหน้ารีวิว"></div>
        </div>
    </div>
</section>

<section class="section section-alt home-steps-section">
    <div class="container">
        <div class="section-header">
            <h2>ขั้นตอนการเรียน</h2>
            <p>เริ่มต้นเรียนภาษาจีนกับ Wenxin ได้ใน 4 ขั้นตอนง่ายๆ</p>
        </div>
        <div class="home-steps-flow">
            <div class="home-step-node">
                <div class="home-step-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M19 8v6"></path><path d="M22 11h-6"></path></svg>
                </div>
                <h3>สมัครสมาชิก</h3>
                <p>ลงทะเบียนด้วยเบอร์โทรและเลือกคอร์สที่ต้องการ</p>
            </div>
            <div class="home-step-connector" aria-hidden="true"></div>
            <div class="home-step-node">
                <div class="home-step-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>
                </div>
                <h3>ชำระเงิน</h3>
                <p>โอนเงิน สแกน PromptPay หรือชำระออนไลน์</p>
            </div>
            <div class="home-step-connector" aria-hidden="true"></div>
            <div class="home-step-node">
                <div class="home-step-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>
                </div>
                <h3>เรียนออนไลน์</h3>
                <p>ดูวิดีโอและเอกสารได้ทุกที่ทุกเวลา</p>
            </div>
            <div class="home-step-connector" aria-hidden="true"></div>
            <div class="home-step-node">
                <div class="home-step-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                </div>
                <h3>รับใบประกาศ</h3>
                <p>เรียนครบและผ่าน Quiz รับใบประกาศนียบัตร</p>
            </div>
        </div>
    </div>
</section>

<section class="section home-faq-section" id="faq">
    <div class="container">
        <div class="section-header-row">
            <div class="section-header section-header-left">
                <h2>คำถามที่พบบ่อย</h2>
                <p>คำตอบสำหรับคำถามที่ผู้เรียนถามบ่อยที่สุด</p>
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
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="7" y="14" width="34" height="24" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M7 16l17 12L41 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 14V11.5c0-.83.67-1.5 1.5-1.5h9c.83 0 1.5.67 1.5 1.5V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M19 11l2-2.5 2 1.5 2-1.5 2 2.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="home-newsletter-copy">
                <h2>รับข่าวสารและโปรโมชั่นพิเศษ</h2>
                <p>ไม่พลาดทุกคอร์สเรียนและกิจกรรมดีๆ</p>
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
                    <input type="email" id="newsletter-email" name="email" placeholder="อีเมลของคุณ" required autocomplete="email">
                    <button type="submit">สมัครรับข่าวสาร</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
