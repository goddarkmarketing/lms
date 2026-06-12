<?php
/** @var array $course */
$highlights = !empty($course['highlights']) ? explode('|', $course['highlights']) : [];
$coverUrl = courseCoverUrl($course);
$detailUrl = APP_URL . '/public/course.php?slug=' . urlencode($course['slug']);
$addToCartUrl = courseEnrollUrl($course);
$buyUrl = APP_URL . '/public/cart_buy.php?course_id=' . (int) ($course['id'] ?? 0);
?>
<article class="course-card" data-category="<?= e($course['category']) ?>">
    <a href="<?= e($detailUrl) ?>" class="course-card-image-link">
        <div class="course-card-image">
            <img src="<?= e($coverUrl) ?>" alt="<?= e($course['title']) ?>" loading="lazy">
            <div class="course-card-image-overlay">
                <span class="badge badge-red"><?= e(categoryLabel($course['category'])) ?></span>
                <span class="badge badge-gold"><?= e(levelBadge($course['level'])) ?></span>
            </div>
        </div>
    </a>
    <div class="course-card-body">
        <h3 class="course-card-title">
            <a href="<?= e($detailUrl) ?>"><?= e($course['title']) ?></a>
        </h3>
        <?php if (!empty($course['subtitle'])): ?>
            <p class="course-card-sub"><?= e($course['subtitle']) ?></p>
        <?php endif; ?>
        <p class="course-card-desc"><?= e(mb_strimwidth(strip_tags($course['description'] ?? ''), 0, 100, '...')) ?></p>
        <?php if ($highlights): ?>
            <ul class="course-tags">
                <?php foreach (array_slice($highlights, 0, 3) as $tag): ?>
                    <li><?= e(trim($tag)) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div class="course-mini-meta" aria-label="ข้อมูลคอร์ส">
            <div class="course-mini-item" title="จำนวนบทเรียน">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path>
                    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path>
                    <path d="M8 6h8"></path>
                    <path d="M8 10h8"></path>
                </svg>
                <span><?= (int) ($course['lesson_count'] ?? 0) ?></span>
            </div>
            <div class="course-mini-item" title="ระยะเวลา">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v6l3 2"></path>
                </svg>
                <span><?= (int) ($course['duration_hours'] ?? 0) ?> ชม.</span>
            </div>
            <div class="course-mini-item" title="หมวดหมู่">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 19V5"></path>
                    <path d="M8 19V9"></path>
                    <path d="M12 19V13"></path>
                    <path d="M16 19V7"></path>
                    <path d="M20 19V11"></path>
                </svg>
                <span><?= e(categoryLabel($course['category'])) ?></span>
            </div>
        </div>
        <div class="course-card-footer">
            <div class="course-price"><?= e(formatPrice((float) ($course['price'] ?? 0))) ?></div>
            <div class="course-stats">
                <?php if (!empty($course['lesson_count'])): ?>
                    <span><?= (int) $course['lesson_count'] ?> บท</span>
                <?php endif; ?>
                <?php if (!empty($course['duration_hours'])): ?>
                    <span><?= (int) $course['duration_hours'] ?> ชม.</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="course-card-actions">
            <a href="<?= e($addToCartUrl) ?>" class="course-buy-icon-btn js-cart-add" aria-label="เพิ่มลงตะกร้า">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6h15l-1.5 9h-12z"></path>
                    <path d="M6 6l-2-2"></path>
                    <circle cx="9" cy="20" r="1"></circle>
                    <circle cx="18" cy="20" r="1"></circle>
                </svg>
            </a>
            <a href="<?= e($buyUrl) ?>" class="course-buy-main-btn">ซื้อคอร์สนี้</a>
        </div>
    </div>
</article>
