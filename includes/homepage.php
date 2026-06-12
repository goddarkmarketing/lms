<?php
declare(strict_types=1);

function getHomepageStats(): array
{
    $stats = [
        'students' => 0,
        'courses' => 0,
        'lessons' => 0,
        'certificates' => 0,
    ];
    try {
        $stats['students'] = (int) db()->query('SELECT COUNT(*) FROM students')->fetchColumn();
        $stats['courses'] = (int) db()->query('SELECT COUNT(*) FROM courses WHERE is_active = 1')->fetchColumn();
        $stats['lessons'] = (int) db()->query('SELECT COUNT(*) FROM lessons WHERE is_published = 1')->fetchColumn();
        $stats['certificates'] = (int) db()->query('SELECT COUNT(*) FROM certificates')->fetchColumn();
    } catch (Throwable $e) {
    }
    return $stats;
}

function getPreviewLessonsForHome(int $limit = 4): array
{
    try {
        $stmt = db()->prepare('
            SELECT l.id, l.title, l.duration_minutes, c.title AS course_title, c.slug AS course_slug, c.id AS course_id
            FROM lessons l
            JOIN courses c ON c.id = l.course_id
            WHERE l.is_free_preview = 1 AND l.is_published = 1 AND c.is_active = 1
            ORDER BY c.sort_order ASC, l.sort_order ASC, l.id ASC
            LIMIT ?
        ');
        $stmt->execute([max(1, $limit)]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function getFirstPreviewLessonUrl(): string
{
    $lessons = getPreviewLessonsForHome(1);
    if ($lessons) {
        return APP_URL . '/public/lesson.php?lesson_id=' . (int) $lessons[0]['id'];
    }
    return APP_URL . '/public/courses.php';
}

function getFaqPageItems(): array
{
    return [
        [
            'q' => 'ไม่เคยเรียนภาษาจีนมาก่อน ควรเริ่มคอร์สไหน?',
            'a' => 'แนะนำเริ่มจากคอร์สพินอิน HSK 1 เพื่อปูพื้นฐานเสียงและคำศัพท์ จากนั้นค่อยต่อ HSK 2–3 ตามเป้าหมาย หรือทดลองเรียนบทฟรีก่อนตัดสินใจซื้อคอร์ส',
        ],
        [
            'q' => 'สมัครสมาชิกต้องใช้อะไรบ้าง?',
            'a' => 'ใช้เบอร์โทรศัพท์มือถือ ชื่อ-นามสกุล และตั้งรหัสผ่าน สมัครได้ที่หน้า「สมัครสมาชิก」ใช้เวลาไม่กี่นาที ไม่ต้องแนบเอกสาร',
        ],
        [
            'q' => 'ชำระเงินแล้วกี่วันถึงจะเปิดสิทธิ์เรียน?',
            'a' => 'การโอนเงินและแจ้งสลิป ทีมงานจะตรวจสอบและเปิดสิทธิ์ภายใน 24 ชั่วโมงทำการ หากชำระผ่านช่องทางออนไลน์ (Omise) ระบบเปิดสิทธิ์อัตโนมัติทันทีเมื่อชำระสำเร็จ',
        ],
        [
            'q' => 'ชำระเงินด้วยวิธีใดบ้าง?',
            'a' => 'รองรับโอนเงินผ่านธนาคาร สแกน PromptPay แนบสลิป และชำระออนไลน์ด้วยบัตร/PromptPay ผ่าน Omise (เปิดสิทธิ์ทันที)',
        ],
        [
            'q' => 'ซื้อหลายคอร์สในครั้งเดียวได้ไหม?',
            'a' => 'ได้ครับ เพิ่มคอร์สลงตะกร้าได้หลายคอร์ส แล้วชำระเงินครั้งเดียวในหน้า「ชำระเงิน」ทีมงานจะเปิดสิทธิ์ทุกคอร์สที่ชำระแล้ว',
        ],
        [
            'q' => 'ใช้คูปองส่วนลดอย่างไร?',
            'a' => 'กรอกรหัสคูปองในหน้าชำระเงินก่อนยืนยันการโอน ระบบจะหักส่วนลดจากยอดรวมอัตโนมัติ หากคูปองใช้ไม่ได้ ตรวจสอบวันหมดอายุหรือเงื่อนไขคอร์สที่ร่วมรายการ',
        ],
        [
            'q' => 'เรียนบนมือถือหรือแท็บเล็ตได้ไหม?',
            'a' => 'ได้ครับ เว็บไซต์รองรับมือถือและแท็บเล็ต ดูวิดีโอและเอกสารได้ทุกที่ทุกเวลา หลังเข้าสู่ระบบแล้วไปที่หน้า「เริ่มเรียน」',
        ],
        [
            'q' => 'เรียนได้นานแค่ไหนหลังซื้อคอร์ส?',
            'a' => 'หลังเปิดสิทธิ์แล้วเข้าเรียนได้ตลอดอายุการใช้งานบัญชี ดูวิดีโอย้อนหลังได้ไม่จำกัดจำนวนครั้งภายในคอร์สที่ซื้อแล้ว',
        ],
        [
            'q' => 'ทดลองเรียนก่อนซื้อได้ไหม?',
            'a' => 'ได้ครับ หลายคอร์สมีบท「ทดลองเรียนฟรี」ให้ดูวิดีโอได้โดยไม่ต้องซื้อ ดูรายการบททดลองได้ที่หน้ารายละเอียดคอร์ส',
        ],
        [
            'q' => 'มีใบประกาศนียบัตรเมื่อจบคอร์สไหม?',
            'a' => 'มีครับ เมื่อเรียนครบ 100% และผ่านเงื่อนไขแบบทดสอบ (ถ้าคอร์สมี Quiz) ระบบออกใบประกาศนียบัตรให้อัตโนมัติ ดาวน์โหลดและพิมพ์ได้จากหน้าโปรไฟล์',
        ],
        [
            'q' => 'ทำแบบทดสอบท้ายคอร์สกี่ครั้งก็ได้ไหม?',
            'a' => 'ทำซ้ำได้ตามที่คอร์สกำหนด ระบบบันทึกคะแนนล่าสุด หากคอร์สตั้งค่าให้ต้องผ่าน Quiz ก่อนรับใบประกาศ ต้องทำให้ผ่านเกณฑ์ที่กำหนด',
        ],
        [
            'q' => 'ลืมรหัสผ่านทำอย่างไร?',
            'a' => 'ไปที่หน้า「ลืมรหัสผ่าน」จากหน้าเข้าสู่ระบบ กรอกเบอร์โทรที่ลงทะเบียน ระบบจะส่งลิงก์รีเซ็ตรหัสผ่านทางอีเมล (ต้องตั้งค่า SMTP ในระบบ)',
        ],
        [
            'q' => 'คืนเงินหรือยกเลิกคอร์สได้ไหม?',
            'a' => 'เมื่อเปิดสิทธิ์เรียนและเข้าถึงเนื้อหาแล้ว โดยทั่วไปไม่สามารถคืนเงินได้ หากมีปัญหาการชำระเงินซ้ำหรือเปิดสิทธิ์ผิดพลาด ติดต่อทีมงานภายใน 7 วัน',
        ],
        [
            'q' => 'ติดต่อทีมงานได้ช่องทางไหนบ้าง?',
            'a' => 'ติดต่อได้ผ่าน Line, Facebook, โทรศัพท์ หรืออีเมลตามข้อมูลในหน้า「ติดต่อเรา」ท้ายเว็บไซต์ ทีมงานตอบกลับในวันและเวลาทำการ',
        ],
    ];
}

/** คำถามเพิ่มเติมเฉพาะหน้าแรก */
function getHomepageExtraFaqItems(): array
{
    return [
        [
            'q' => 'HSK คืออะไร ทำไมควรเรียนให้ครบระดับ?',
            'a' => 'HSK (汉语水平考试) คือการสอบวัดระดับภาษาจีนที่ได้รับการยอมรับทั่วโลก การเรียนตามลำดับ HSK 1–5 ช่วยปูพื้นฐานไปสู่การใช้งานจริงและเตรียมสอบได้อย่างเป็นระบบ',
        ],
        [
            'q' => 'มีเอกสารประกอบการเรียนไหม?',
            'a' => 'มีครับ หลายบทเรียนมีไฟล์เอกสารแนบให้ดาวน์โหล์ได้ในหน้าบทเรียน สามารถเก็บไว้ทบทวนหรือพิมพ์ออกมาเรียนได้ตามสะดวก',
        ],
        [
            'q' => 'ดูความคืบหน้าการเรียนได้ที่ไหน?',
            'a' => 'หลังเข้าสู่ระบบไปที่หน้า「คอร์สของฉัน」หรือ「เริ่มเรียน」ระบบแสดงเปอร์เซ็นต์ความคืบหน้าแต่ละคอร์ส และบทที่เรียนแล้ว/ยังไม่เรียน',
        ],
        [
            'q' => 'แจ้งชำระเงินแล้วแต่ยังเข้าเรียนไม่ได้ทำอย่างไร?',
            'a' => 'ตรวจสอบว่าสลิปถูกต้องและยอดเงินตรงกับคอร์สที่สั่งซื้อ จากนั้นติดต่อทีมงานผ่าน Line พร้อมเบอร์โทรที่สมัครและหลักฐานการโอน ทีมงานจะตรวจสอบและเปิดสิทธิ์ให้โดยเร็ว',
        ],
        [
            'q' => 'สมัครเรียนให้บุตรหลานหรือญาติได้ไหม?',
            'a' => 'ได้ครับ ให้ผู้เรียนสมัครบัญชีด้วยเบอร์โทรของตนเอง หรือผู้ปกครองสมัครแล้วชำระเงินแทนได้ แต่ควรใช้บัญชีของผู้เรียนจริงเพื่อติดตามความคืบหน้าและรับใบประกาศ',
        ],
        [
            'q' => 'ต้องเรียนตามลำดับบทหรือข้ามบทได้?',
            'a' => 'แนะนำเรียนตามลำดับบทเพื่อความต่อเนื่องของเนื้อหา โดยเฉพาะคอร์สพื้นฐาน หากมีพื้นฐานมาแล้วสามารถเลือกบทที่ต้องการทบทวนได้ภายในคอร์สที่ซื้อแล้ว',
        ],
    ];
}

function getHomepageFaqItems(): array
{
    return array_merge(
        array_slice(getFaqPageItems(), 0, 6),
        getHomepageExtraFaqItems()
    );
}

function getLearningPathItems(): array
{
    $paths = [
        ['slug' => 'hsk1-pinyin', 'level' => 'เริ่มต้น', 'label' => 'พินอิน + HSK 1'],
        ['slug' => 'hsk2', 'level' => 'ระดับ 2', 'label' => 'HSK 2'],
        ['slug' => 'hsk3', 'level' => 'ระดับ 3', 'label' => 'HSK 3'],
        ['slug' => 'hsk4', 'level' => 'ระดับ 4', 'label' => 'HSK 4'],
        ['slug' => 'hsk5', 'level' => 'ระดับ 5', 'label' => 'HSK 5'],
        ['slug' => 'exam-prep-hsk3', 'level' => 'ติวสอบ', 'label' => 'ติว HSK 3'],
    ];
    $result = [];
    foreach ($paths as $path) {
        $course = getCourseBySlug($path['slug']);
        if ($course) {
            $result[] = array_merge($path, [
                'title' => $course['title'],
                'url' => APP_URL . '/public/course.php?slug=' . urlencode($path['slug']),
            ]);
        }
    }
    return $result;
}

function getFooterCourseLinks(int $limit = 8): array
{
    try {
        $links = [];
        foreach (array_slice(getCourses(), 0, max(1, $limit)) as $course) {
            $links[] = [
                'title' => (string) ($course['title'] ?? ''),
                'url' => APP_URL . '/public/course.php?slug=' . urlencode((string) ($course['slug'] ?? '')),
            ];
        }
        return $links;
    } catch (Throwable $e) {
        return [];
    }
}

function getFooterHskCourseLinks(): array
{
    $labels = [
        'hsk1-pinyin' => 'HSK 1',
        'hsk2' => 'HSK 2',
        'hsk3' => 'HSK 3',
        'hsk4' => 'HSK 4',
        'hsk5' => 'HSK 5',
    ];
    try {
        $courses = getCourses();
        $bySlug = [];
        foreach ($courses as $course) {
            $bySlug[(string) ($course['slug'] ?? '')] = $course;
        }
        $links = [];
        foreach ($labels as $slug => $label) {
            if (!isset($bySlug[$slug])) {
                continue;
            }
            $links[] = [
                'title' => $label,
                'url' => APP_URL . '/public/course.php?slug=' . urlencode($slug),
            ];
        }
        return $links;
    } catch (Throwable $e) {
        return [];
    }
}

function subscribeNewsletter(string $email): array
{
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'กรุณากรอกอีเมลให้ถูกต้อง'];
    }
    try {
        db()->exec('
            CREATE TABLE IF NOT EXISTS newsletter_subscribers (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              email VARCHAR(255) NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY uk_newsletter_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
        $stmt = db()->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?)');
        $stmt->execute([$email]);
        return ['ok' => true, 'message' => 'สมัครรับข่าวสารเรียบร้อยแล้ว ขอบคุณครับ'];
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return ['ok' => true, 'message' => 'อีเมลนี้สมัครรับข่าวสารไว้แล้ว'];
        }
        return ['ok' => false, 'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'];
    } catch (Throwable $e) {
        return ['ok' => false, 'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'];
    }
}

function lineContactUrl(): string
{
    $lineId = trim(getSetting('line_id', ''));
    if ($lineId === '') {
        return APP_URL . '/public/index.php#contact';
    }
    if (str_starts_with($lineId, 'http')) {
        return $lineId;
    }
    $id = ltrim($lineId, '@');
    return 'https://line.me/ti/p/~' . rawurlencode($id);
}
