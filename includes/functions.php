<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . APP_URL . $path);
    exit;
}

function isSafeLocalReturn(string $path): bool
{
    if ($path === '' || str_contains($path, '://') || str_contains($path, '..')) {
        return false;
    }
    if (!str_starts_with($path, '/public/')) {
        return false;
    }
    if (str_contains($path, '/public/cart_add.php') || str_contains($path, '/public/cart_remove.php')) {
        return false;
    }
    return true;
}

function currentReturnPath(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/public/index.php';
    $uri = preg_replace('#^/LMS#', '', $uri) ?: '/public/index.php';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/public/index.php';
    $query = parse_url($uri, PHP_URL_QUERY);
    $target = $path . ($query ? '?' . $query : '');
    return isSafeLocalReturn($target) ? $target : '/public/index.php';
}

function redirectBack(string $defaultPath = '/public/courses.php'): void
{
    $return = trim($_GET['return'] ?? '');
    if ($return !== '' && isSafeLocalReturn($return)) {
        $return = strtok($return, '#') ?: $return;
        redirect($return);
    }

    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        $path = parse_url($ref, PHP_URL_PATH) ?: '';
        if ($path !== '' && (str_contains($path, '/LMS/') || str_ends_with($path, '/LMS'))) {
            $local = preg_replace('#^/LMS#', '', $path) ?: '/';
            $query = parse_url($ref, PHP_URL_QUERY);
            $target = $local . ($query ? '?' . $query : '');
            if (isSafeLocalReturn($target)) {
                redirect($target);
            }
        }
    }
    redirect($defaultPath);
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('คำขอไม่ถูกต้อง กรุณารีเฟรชหน้าแล้วลองใหม่');
    }
}

const MAX_SLIP_UPLOAD_BYTES = 5 * 1024 * 1024;

function validateSlipUpload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return 'อัปโหลดไฟล์ไม่สำเร็จ';
    }
    if (($file['size'] ?? 0) > MAX_SLIP_UPLOAD_BYTES) {
        return 'ไฟล์ใหญ่เกิน 5MB';
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
    if (!in_array($ext, $allowedExt, true)) {
        return 'รองรับเฉพาะ JPG, PNG, GIF, WEBP, PDF';
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMime = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf',
    ];
    if (!in_array($mime, $allowedMime, true)) {
        return 'ชนิดไฟล์ไม่รองรับ';
    }
    return null;
}

function storeSlipUpload(array $file): string|false|null
{
    $error = validateSlipUpload($file);
    if ($error !== null) {
        flash('payment_error', $error);
        return false;
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    $filename = 'slip_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = UPLOAD_PATH . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        flash('payment_error', 'บันทึกไฟล์สลิปไม่สำเร็จ');
        return false;
    }
    return $filename;
}

function asset(string $path): string
{
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function imageAsset(string $relativePath, string $fallbackPath = ''): string
{
    $full = BASE_PATH . '/assets/' . ltrim($relativePath, '/');
    if (is_file($full)) {
        return asset($relativePath);
    }
    if ($fallbackPath !== '') {
        return asset($fallbackPath);
    }
    return asset($relativePath);
}

function brandLogoAsset(): string
{
    return imageAsset('images/logo.png', 'images/logo.svg');
}

function headingFontAsset(): string
{
    return APP_URL . '/assets/fonts/DB-Adman-X.ttf';
}

function fontAsset(): string
{
    return headingFontAsset();
}

function iconAsset(string $filename): string
{
    return APP_URL . '/assets/icon/' . rawurlencode($filename);
}

function versionedCourseAsset(string $relativePath): string
{
    $relativePath = ltrim($relativePath, '/');
    $full = BASE_PATH . '/assets/' . $relativePath;
    $url = asset($relativePath);
    if (is_file($full)) {
        return $url . '?v=' . filemtime($full);
    }
    return $url;
}

function courseCoverUrl(array $course): string
{
    $slugCovers = [
        'hsk1-pinyin' => 'images/courses/hsk1.png',
        'hsk2' => 'images/courses/hsk2.png',
        'hsk3' => 'images/courses/hsk3.png',
        'hsk4' => 'images/courses/hsk4.png',
        'hsk5' => 'images/courses/hsk5.png',
        'exam-prep-hsk3' => 'images/courses/exam-hsk3.png',
        'exam-prep-hsk4' => 'images/courses/exam-hsk4.png',
        'exam-prep-hsk5' => 'images/courses/exam-hsk5.png',
    ];

    $slug = $course['slug'] ?? '';
    if (isset($slugCovers[$slug]) && is_file(BASE_PATH . '/assets/' . $slugCovers[$slug])) {
        return versionedCourseAsset($slugCovers[$slug]);
    }

    if (!empty($course['image_url'])) {
        $url = $course['image_url'];
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        if (str_starts_with($url, 'uploads/courses/')) {
            return APP_URL . '/public/download.php?file=' . urlencode(basename($url));
        }
        return versionedCourseAsset(ltrim($url, '/'));
    }

    $categoryCovers = [
        'foundation' => 'images/courses/cover-foundation.svg',
        'hsk' => 'images/courses/cover-hsk.svg',
        'exam_prep' => 'images/courses/cover-exam.svg',
    ];

    return versionedCourseAsset($categoryCovers[$course['category'] ?? 'hsk'] ?? 'images/courses/cover-hsk.svg');
}

function courseEnrollUrl(array $course): string
{
    $return = urlencode(currentReturnPath());
    return APP_URL . '/public/cart_add.php?course_id=' . urlencode((string) ($course['id'] ?? '')) . '&return=' . $return;
}

function courseBuyUrl(array $course): string
{
    return APP_URL . '/public/cart_buy.php?course_id=' . (int) ($course['id'] ?? 0);
}

function courseStartLessonUrl(array $course, array $lessons): string
{
    foreach ($lessons as $lesson) {
        if (!empty($lesson['is_free_preview'])) {
            return APP_URL . '/public/lesson.php?lesson_id=' . (int) ($lesson['id'] ?? 0);
        }
    }
    if (!empty($lessons[0]['id'])) {
        return APP_URL . '/public/lesson.php?lesson_id=' . (int) $lessons[0]['id'];
    }
    return APP_URL . '/public/course.php?slug=' . urlencode((string) ($course['slug'] ?? ''));
}

function formatDurationMinutes(int $minutes): string
{
    if ($minutes <= 0) {
        return '';
    }
    if ($minutes < 60) {
        return $minutes . ' นาที';
    }
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return $mins > 0 ? $hours . ' ชม. ' . $mins . ' นาที' : $hours . ' ชม.';
}

/** @return array{video:int,doc:int,preview:int,totalMinutes:int} */
function courseLessonStats(array $lessons): array
{
    $video = 0;
    $doc = 0;
    $preview = 0;
    $totalMinutes = 0;
    foreach ($lessons as $lesson) {
        if (!empty($lesson['video_url'])) {
            $video++;
        }
        if (!empty($lesson['document_url'])) {
            $doc++;
        }
        if (!empty($lesson['is_free_preview'])) {
            $preview++;
        }
        $totalMinutes += (int) ($lesson['duration_minutes'] ?? 0);
    }
    return [
        'video' => $video,
        'doc' => $doc,
        'preview' => $preview,
        'totalMinutes' => $totalMinutes,
    ];
}

function courseAudienceBullets(array $course): array
{
    $category = $course['category'] ?? 'hsk';
    $level = $course['level'] ?? 'beginner';

    if ($category === 'foundation') {
        return [
            'ผู้เริ่มเรียนภาษาจีน ไม่ต้องมีพื้นฐานมาก่อน',
            'ผู้ที่ต้องการเรียนพินอินและเตรียมสอบ HSK 1',
            'นักเรียน นักศึกษา หรือผู้ทำงานที่อยากเริ่มภาษาจีนอย่างถูกต้อง',
        ];
    }
    if ($category === 'exam_prep') {
        return [
            'ผู้ที่เรียนครบระดับแล้วและกำลังเตรียมสอบ HSK',
            'ผู้ที่ต้องการฝึกทำข้อสอบและทบทวนจุดอ่อนก่อนสอบจริง',
            'ผู้ที่ต้องการเทคนิคการทำข้อสอบและจัดการเวลาในห้องสอบ',
        ];
    }

    return match ($level) {
        'intermediate' => [
            'ผู้ที่มีพื้นฐานภาษาจีนระดับต้นแล้ว',
            'ผู้ที่ต้องการพัฒนาทักษะสื่อสารและอ่านเข้าใจระดับกลาง',
            'ผู้ที่เตรียมสอบ HSK ระดับกลาง',
        ],
        'advanced' => [
            'ผู้ที่มีพื้นฐานภาษาจีนระดับกลางขึ้นไป',
            'ผู้ที่ต้องการพัฒนาทักษะระดับสูงเพื่อการเรียน การทำงาน หรือสอบ HSK',
            'ผู้ที่ต้องการอ่านและเขียนภาษาจีนในบริบทที่ซับซ้อนขึ้น',
        ],
        default => [
            'ผู้ที่เรียนภาษาจีนมาบ้างแล้วและต้องการต่อยอด',
            'ผู้ที่ต้องการเสริมคำศัพท์ ไวยากรณ์ และทักษะสื่อสาร',
            'ผู้ที่เตรียมสอบ HSK ในระดับถัดไป',
        ],
    };
}

function courseIncludedItems(): array
{
    return [
        ['icon' => 'video', 'title' => 'วิดีโอบทเรียนออนไลน์', 'desc' => 'เรียนทีละบท ดูซ้ำได้ตามต้องการ'],
        ['icon' => 'doc', 'title' => 'เอกสารประกอบ', 'desc' => 'ทบทวนและดาวน์โหลดตามที่อาจารย์แนบในแต่ละบท'],
        ['icon' => 'device', 'title' => 'เรียนได้ทุกอุปกรณ์', 'desc' => 'เข้าเรียนผ่านมือถือ แท็บเล็ต หรือคอมพิวเตอร์'],
        ['icon' => 'support', 'title' => 'สอบถามทีมงาน', 'desc' => 'ติดต่อผ่าน Line / Facebook หลังแจ้งชำระเงิน'],
    ];
}

function courseFaqItems(): array
{
    return [
        [
            'q' => 'หลังชำระเงินแล้ว เปิดสิทธิ์เรียนเมื่อไหร่?',
            'a' => 'ทีมงานจะตรวจสอบสลิปและเปิดสิทธิ์ให้โดยเร็วที่สุด โดยปกติภายใน 1–2 วันทำการหลังแจ้งชำระเงิน',
        ],
        [
            'q' => 'เรียนซ้ำหรือย้อนดูบทเรียนได้ไหม?',
            'a' => 'ได้ครับ สามารถกลับมาดูวิดีโอและทบทวนเนื้อหาในบทที่เรียนแล้วได้ตลอด',
        ],
        [
            'q' => 'มีบททดลองเรียนฟรีไหม?',
            'a' => 'บทที่ระบุป้าย "ทดลองเรียนฟรี" สามารถเข้าเรียนได้ก่อนสมัครคอร์ส (ถ้ามีในคอร์สนั้น)',
        ],
        [
            'q' => 'ชำระเงินผ่านช่องทางไหนได้บ้าง?',
            'a' => 'โอนเงินตามบัญชีที่ระบบแสดง แล้วแจ้งหลักฐานการโอนผ่านฟอร์มชำระเงินบนเว็บไซต์',
        ],
        [
            'q' => 'สมัครหลายคอร์สพร้อมกันได้ไหม?',
            'a' => 'ได้ครับ เพิ่มคอร์สลงตะกร้าแล้วชำระรวมในครั้งเดียวได้',
        ],
    ];
}

function getRelatedCourses(array $course, int $limit = 3): array
{
    $courseId = (int) ($course['id'] ?? 0);
    $category = $course['category'] ?? 'hsk';
    $related = [];

    $stmt = db()->prepare('
        SELECT * FROM courses
        WHERE is_active = 1 AND id != ? AND category = ?
        ORDER BY sort_order ASC, id ASC
        LIMIT ' . (int) $limit
    );
    $stmt->execute([$courseId, $category]);
    $related = $stmt->fetchAll();

    if (count($related) >= $limit) {
        return $related;
    }

    $excludeIds = array_merge([$courseId], array_map(fn($c) => (int) $c['id'], $related));
    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
    $stmt = db()->prepare("
        SELECT * FROM courses
        WHERE is_active = 1 AND id NOT IN ({$placeholders})
        ORDER BY sort_order ASC, id ASC
        LIMIT " . (int) ($limit - count($related))
    );
    $stmt->execute($excludeIds);
    return array_merge($related, $stmt->fetchAll());
}

function formatPrice(?float $price): string
{
    if ($price === null || $price <= 0) {
        return 'ติดต่อสอบถาม';
    }
    return number_format($price, 0) . ' บาท';
}

function getSettings(): array
{
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    try {
        $stmt = db()->query('SELECT setting_key, setting_value FROM site_settings');
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (Throwable) {
        return [
            'site_title' => 'Wenxin Chinese',
            'site_tagline' => 'เหวินซิน ปั้นภาษาจีนให้เป็นเรื่องง่าย',
            'bank_account_name' => 'นางสาว พวงพกา แสนคำแพ',
            'bank_name' => 'กสิกร',
            'bank_account_number' => '0691172899',
            'payment_note' => 'กรุณาแจ้งหลักฐานการโอน เพื่อความรวดเร็วในการดำเนินการ ขอบคุณค่ะ',
            'facebook_url' => 'https://www.facebook.com/profile.php?id=61567439751026',
            'line_id' => '@janeyangpeiling',
            'phone' => '0895567438',
        ];
    }
}

function getSetting(string $key, string $default = ''): string
{
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

function getCourses(?string $category = null, bool $activeOnly = true, ?string $search = null): array
{
    $sql = 'SELECT * FROM courses WHERE 1=1';
    $params = [];

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }
    if ($category) {
        $sql .= ' AND category = ?';
        $params[] = $category;
    }
    if ($search !== null && $search !== '') {
        $sql .= ' AND (title LIKE ? OR subtitle LIKE ? OR description LIKE ?)';
        $like = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= ' ORDER BY sort_order ASC, id ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getCourseBySlug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM courses WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $course = $stmt->fetch();
    return $course ?: null;
}

function getCourseById(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM courses WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $course = $stmt->fetch();
    return $course ?: null;
}

function getLessonsByCourse(int $courseId): array
{
    $stmt = db()->prepare('SELECT * FROM lessons WHERE course_id = ? AND is_published = 1 ORDER BY sort_order ASC, id ASC');
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

function categoryLabel(string $category): string
{
    return match ($category) {
        'foundation' => 'พื้นฐาน',
        'hsk' => 'HSK',
        'exam_prep' => 'ติวสอบ',
        default => 'ทั่วไป',
    };
}

function levelBadge(string $level): string
{
    return match ($level) {
        'beginner' => 'เริ่มต้น',
        'intermediate' => 'ปานกลาง',
        'advanced' => 'ขั้นสูง',
        default => 'ทุกระดับ',
    };
}
