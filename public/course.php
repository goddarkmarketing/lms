<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/access.php';
require_once dirname(__DIR__) . '/includes/instructor.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    redirect('/public/courses.php');
}

$course = null;
$lessons = [];
try {
    $course = getCourseBySlug($slug);
    if ($course) {
        $lessons = getLessonsByCourse((int) $course['id']);
    }
} catch (Throwable $e) {
    $course = null;
}

if (!$course) {
    $pageTitle = 'ไม่พบคอร์ส';
    require_once dirname(__DIR__) . '/includes/header.php';
    echo '<section class="section"><div class="container"><p class="lesson-empty">ไม่พบคอร์สที่ต้องการ</p><a href="' . APP_URL . '/public/courses.php" class="btn btn-primary">กลับหน้าคอร์ส</a></div></section>';
    require_once dirname(__DIR__) . '/includes/footer.php';
    exit;
}

$pageTitle = $course['title'];
$courseDetail = $course;
$highlights = !empty($course['highlights']) ? explode('|', $course['highlights']) : [];
$lessonStats = courseLessonStats($lessons);
$startUrl = courseStartLessonUrl($course, $lessons);
$relatedCourses = getRelatedCourses($course, 3);
$audienceBullets = courseAudienceBullets($course);
$includedItems = courseIncludedItems();
$faqItems = courseFaqItems();
$hasLessons = count($lessons) > 0;

$instructor = getInstructorProfile();
$instructorPhotoUrl = instructorPhotoUrl($instructor);

require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="course-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= APP_URL ?>/public/index.php">หน้าแรก</a> /
            <a href="<?= APP_URL ?>/public/courses.php">คอร์ส</a> /
            <?= e($course['title']) ?>
        </div>
        <div class="course-card-meta" style="margin-bottom:1rem">
            <span class="badge badge-gold"><?= e(categoryLabel($course['category'])) ?></span>
            <span class="badge badge-gold"><?= e(levelBadge($course['level'])) ?></span>
        </div>
        <h1><?= e($course['title']) ?></h1>
        <?php if (!empty($course['subtitle'])): ?>
            <p class="course-hero-sub"><?= e($course['subtitle']) ?></p>
        <?php endif; ?>
        <?php if ($hasLessons): ?>
        <div class="course-hero-actions">
            <a href="<?= e($startUrl) ?>" class="btn btn-gold">เริ่มเรียนบทแรก</a>
            <?php if ($lessonStats['preview'] > 0): ?>
                <span class="course-hero-note">มี <?= (int) $lessonStats['preview'] ?> บททดลองเรียนฟรี</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section course-detail-section">
    <div class="container course-detail-grid">
        <div class="course-detail-main">
            <section class="course-block">
                <h2 class="course-block-title">รายละเอียดคอร์ส</h2>
                <p class="course-desc"><?= nl2br(e($course['description'] ?? '')) ?></p>
            </section>

            <section class="course-block">
                <h2 class="course-block-title">คอร์สนี้เหมาะกับใคร</h2>
                <ul class="course-bullet-list">
                    <?php foreach ($audienceBullets as $bullet): ?>
                        <li><?= e($bullet) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <?php if ($highlights): ?>
            <section class="course-block">
                <h2 class="course-block-title">สิ่งที่จะได้เรียน</h2>
                <ul class="course-tags">
                    <?php foreach ($highlights as $tag): ?>
                        <li><?= e(trim($tag)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <section class="course-block">
                <h2 class="course-block-title">สิ่งที่ได้รับหลังสมัคร</h2>
                <div class="course-included-grid">
                    <?php foreach ($includedItems as $item): ?>
                        <div class="course-included-item">
                            <div class="course-included-icon" aria-hidden="true">
                                <?php if ($item['icon'] === 'video'): ?>
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <?php elseif ($item['icon'] === 'doc'): ?>
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg>
                                <?php elseif ($item['icon'] === 'device'): ?>
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"></rect><path d="M12 18h.01"></path></svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong><?= e($item['title']) ?></strong>
                                <p><?= e($item['desc']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="course-block">
                <h2 class="course-block-title">หลักสูตร / บทเรียนในคอร์ส</h2>
                <?php if ($lessons): ?>
                    <ul class="lesson-list lesson-list-enhanced">
                        <?php foreach ($lessons as $i => $lesson): ?>
                            <?php
                            $mins = (int) ($lesson['duration_minutes'] ?? 0);
                            $durationLabel = formatDurationMinutes($mins);
                            $isPreview = !empty($lesson['is_free_preview']);
                            $lessonRow = array_merge($lesson, ['course_id' => (int) $course['id']]);
                            $canAccess = canAccessLesson($lessonRow);
                            ?>
                            <li class="lesson-list-item<?= $canAccess ? '' : ' lesson-list-item--locked' ?>">
                                <div class="lesson-list-index"><?= $i + 1 ?></div>
                                <div class="lesson-list-body">
                                    <div class="lesson-list-head">
                                        <?php if ($canAccess): ?>
                                        <a href="<?= APP_URL ?>/public/lesson.php?lesson_id=<?= (int) ($lesson['id'] ?? 0) ?>" class="lesson-list-title">
                                            <?= e($lesson['title']) ?>
                                        </a>
                                        <?php else: ?>
                                        <span class="lesson-list-title lesson-list-title--locked"><?= e($lesson['title']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($isPreview): ?>
                                            <span class="badge badge-gold lesson-preview-badge">ทดลองเรียนฟรี</span>
                                        <?php elseif (!$canAccess): ?>
                                            <span class="badge lesson-lock-badge" title="ต้องซื้อคอร์สก่อนเรียน">
                                                <svg class="icon-lock" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
                                                ล็อก
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($lesson['description'])): ?>
                                        <p class="lesson-list-desc"><?= e($lesson['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="lesson-list-meta">
                                        <?php if ($durationLabel): ?>
                                            <span class="lesson-meta-tag">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l3 2"></path></svg>
                                                <?= e($durationLabel) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['video_url'])): ?>
                                            <span class="lesson-meta-tag">วิดีโอ</span>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['document_url'])): ?>
                                            <span class="lesson-meta-tag">เอกสาร</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="lesson-empty">บทเรียนจะเพิ่มเติมเร็วๆ นี้ สามารถเพิ่มวิดีโอและเอกสารได้จากหลังบ้าน</div>
                <?php endif; ?>
            </section>

            <section class="course-block course-payment-info">
                <h2 class="course-block-title">วิธีเรียนและชำระเงิน</h2>
                <ol class="course-steps-list">
                    <li>เลือกคอร์สและกด <strong>ซื้อคอร์สนี้</strong> หรือเพิ่มลงตะกร้า</li>
                    <li>โอนเงินตามบัญชี <?= e(getSetting('bank_name')) ?> เลขที่ <?= e(getSetting('bank_account_number')) ?></li>
                    <li>แจ้งหลักฐานการโอนผ่านฟอร์มชำระเงิน</li>
                    <li>ทีมงานเปิดสิทธิ์เรียน แล้วเข้าเรียนทีละบทได้ทันที</li>
                </ol>
                <a href="<?= APP_URL ?>/public/cart.php" class="btn btn-outline btn-sm">ดูตะกร้า / ชำระเงิน</a>
            </section>

            <section class="course-block">
                <h2 class="course-block-title">คำถามที่พบบ่อย</h2>
                <div class="course-faq">
                    <?php foreach ($faqItems as $faq): ?>
                        <details class="course-faq-item">
                            <summary><?= e($faq['q']) ?></summary>
                            <p><?= e($faq['a']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <aside class="course-sidebar">
            <a href="<?= e($startUrl) ?>" class="course-sidebar-cover-link">
                <img src="<?= e(courseCoverUrl($course)) ?>" alt="<?= e($course['title']) ?>" class="course-sidebar-cover">
            </a>

            <ul class="course-sidebar-stats" aria-label="ข้อมูลคอร์ส">
                <?php if (!empty($course['lesson_count'])): ?>
                <li>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path></svg>
                    <span><strong><?= (int) $course['lesson_count'] ?></strong> บทเรียน</span>
                </li>
                <?php endif; ?>
                <?php if (!empty($course['duration_hours'])): ?>
                <li>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l3 2"></path></svg>
                    <span><strong><?= (int) $course['duration_hours'] ?></strong> ชั่วโมงโดยประมาณ</span>
                </li>
                <?php endif; ?>
                <li>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7z"></path></svg>
                    <span>ระดับ <?= e(levelBadge($course['level'])) ?></span>
                </li>
                <?php if ($lessonStats['video'] > 0): ?>
                <li>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <span><strong><?= (int) $lessonStats['video'] ?></strong> บทมีวิดีโอ</span>
                </li>
                <?php endif; ?>
                <?php if ($lessonStats['preview'] > 0): ?>
                <li>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <span><strong><?= (int) $lessonStats['preview'] ?></strong> บททดลองฟรี</span>
                </li>
                <?php endif; ?>
            </ul>

            <?php
            $photoUrl = $instructorPhotoUrl;
            $chipModifier = 'instructor-chip--sidebar';
            require dirname(__DIR__) . '/includes/views/instructor_chip.php';
            ?>

            <div class="course-sidebar-price"><?= e(formatPrice((float) ($course['price'] ?? 0))) ?></div>

            <div class="course-sidebar-actions">
                <?php if ($hasLessons): ?>
                    <a href="<?= e($startUrl) ?>" class="btn btn-gold btn-block btn-sm">เริ่มเรียนบทแรก</a>
                <?php endif; ?>
                <a href="<?= e(courseBuyUrl($course)) ?>" class="btn btn-primary btn-block btn-sm">ซื้อคอร์สนี้</a>
                <a href="<?= e(courseEnrollUrl($course)) ?>" class="btn btn-outline btn-block btn-sm js-cart-add">เพิ่มลงตะกร้า</a>
                <a href="<?= APP_URL ?>/public/index.php#contact" class="btn btn-outline btn-block btn-sm">ติดต่อสอบถาม</a>
            </div>

            <p class="course-sidebar-note"><?= e(getSetting('payment_note')) ?></p>
        </aside>
    </div>
</section>

<?php if ($relatedCourses): ?>
<section class="section section-alt course-related-section">
    <div class="container">
        <div class="section-header">
            <h2>คอร์สที่เกี่ยวข้อง</h2>
            <p>คอร์สอื่นในหมวด <?= e(categoryLabel($course['category'])) ?> ที่อาจเหมาะกับคุณ</p>
        </div>
        <div class="courses-grid">
            <?php foreach ($relatedCourses as $relatedCourse): ?>
                <?php $course = $relatedCourse; include dirname(__DIR__) . '/includes/course_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="course-mobile-bar" aria-hidden="false">
    <div class="course-mobile-bar-price"><?= e(formatPrice((float) ($courseDetail['price'] ?? 0))) ?></div>
    <div class="course-mobile-bar-actions">
        <a href="<?= e(courseEnrollUrl($courseDetail)) ?>" class="btn btn-outline btn-sm js-cart-add" aria-label="เพิ่มลงตะกร้า">ตะกร้า</a>
        <a href="<?= e(courseBuyUrl($courseDetail)) ?>" class="btn btn-primary btn-sm">ซื้อคอร์ส</a>
    </div>
</div>

<script>document.body.classList.add('has-course-mobile-bar');</script>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
