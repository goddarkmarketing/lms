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
                <?= lucide_icon('book-open', ['size' => 18]) ?>
                <span><?= (int) ($course['lesson_count'] ?? 0) ?></span>
            </div>
            <div class="course-mini-item" title="ระยะเวลา">
                <?= lucide_icon('clock', ['size' => 18]) ?>
                <span><?= (int) ($course['duration_hours'] ?? 0) ?> ชม.</span>
            </div>
            <div class="course-mini-item" title="หมวดหมู่">
                <?= lucide_icon('chart-column', ['size' => 18]) ?>
                <span><?= e(categoryLabel($course['category'])) ?></span>
            </div>
        </div>
        <div class="course-card-footer">
            <div class="course-price"><?= e(formatPrice((float) ($course['price'] ?? 0))) ?></div>
        </div>
        <div class="course-card-actions">
            <a href="<?= e($addToCartUrl) ?>" class="course-buy-icon-btn js-cart-add" aria-label="เพิ่มลงตะกร้า">
                <?= lucide_icon('shopping-cart', ['size' => 20]) ?>
            </a>
            <a href="<?= e($buyUrl) ?>" class="course-buy-main-btn">ซื้อคอร์สนี้</a>
        </div>
    </div>
</article>
