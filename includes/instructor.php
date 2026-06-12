<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/homepage.php';

function instructorDefaultCredentials(): array
{
    return [
        'ปริญญาโท การสอนภาษาจีน มหาวิทยาลัยปักกิ่ง',
        'ประสบการณ์สอนภาษาจีนมากกว่า 8 ปี',
        'สอนผู้เรียนออนไลน์มากกว่า 5,000+ คน',
        'เชี่ยวชาญการเตรียมสอบ HSK ตั้งแต่ระดับ 1–5',
    ];
}

function instructorDefaultHighlights(): array
{
    return [
        'สอนเข้าใจง่าย เน้นใช้งานจริงในชีวิตประจำวัน',
        'เนื้อหาเป็นระบบ เรียนซ้ำได้ไม่จำกัดเวลา',
        'แบบฝึกหัดและแบบทดสอบครบทุกคอร์ส',
        'เน้นเทคนิคเตรียมสอบ HSK ที่ใช้ได้จริง',
    ];
}

function instructorParseList(string $value, array $defaults): array
{
    $items = array_values(array_filter(array_map('trim', explode('|', $value)), static fn($item) => $item !== ''));
    return $items ?: $defaults;
}

function getInstructorProfile(?array $homeStats = null): array
{
    $homeStats ??= getHomepageStats();
    $credentials = instructorParseList(
        getSetting('instructor_credentials', implode('|', instructorDefaultCredentials())),
        instructorDefaultCredentials()
    );
    $highlights = instructorParseList(
        getSetting('instructor_highlights', implode('|', instructorDefaultHighlights())),
        instructorDefaultHighlights()
    );

    $studentStat = trim(getSetting('instructor_stat_students', '5000+'));
    if ($studentStat === '' && (int) $homeStats['students'] > 0) {
        $studentStat = number_format((int) $homeStats['students']) . '+';
    }

    return [
        'name' => getSetting('instructor_name', 'อาจารย์เหวินซิน (Wenxin)'),
        'role' => getSetting('instructor_role', 'ผู้สอนหลัก · Wenxin Chinese'),
        'tagline' => getSetting('instructor_tagline', 'ครูภาษาจีนออนไลน์ · ผู้เชี่ยวชาญเตรียมสอบ HSK ทุกระดับ'),
        'bio' => getSetting('instructor_bio', 'อาจารย์เหวินซินเป็นครูภาษาจีนออนไลน์ที่มุ่งเน้นให้ผู้เรียนเข้าใจภาษาจีนอย่างเป็นระบบ ไม่ท่องจำแบบสอนตะกวด ด้วยประสบการณ์สอนทั้งในไทยและต่างประเทศ อาจารย์ออกแบบคอร์สให้เหมาะกับผู้เริ่มต้นจนถึงผู้เตรียมสอบ HSK ระดับสูง เน้นให้ผู้เรียนนำไปใช้ได้จริงทั้งการสื่อสาร การอ่าน และการเตรียมสอบ'),
        'quote' => getSetting('instructor_quote', 'เหวินซิน ปั้นภาษาจีนให้เป็นเรื่องง่าย — สอนด้วยหลักการที่เข้าใจง่ายและนำไปใช้ได้จริง'),
        'photo_path' => trim(getSetting('instructor_photo', '')),
        'credentials' => $credentials,
        'highlights' => $highlights,
        'stats' => [
            [
                'value' => $studentStat ?: '5,000+',
                'label' => 'ผู้เรียนทั่วประเทศ',
                'icon' => 'users',
            ],
            [
                'value' => max(1, (int) $homeStats['courses']) . '+',
                'label' => 'คอร์สออนไลน์',
                'icon' => 'courses',
            ],
            [
                'value' => getSetting('instructor_stat_satisfaction', '95%'),
                'label' => 'ความพึงพอใจผู้เรียน',
                'icon' => 'star',
            ],
            [
                'value' => max(1, (int) $homeStats['lessons']) . '+',
                'label' => 'บทเรียนวิดีโอ',
                'icon' => 'video',
            ],
        ],
    ];
}

function instructorPhotoUrl(array $profile): string
{
    $path = trim($profile['photo_path'] ?? '');
    if ($path !== '') {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'uploads/')) {
            return APP_URL . '/public/' . ltrim($path, '/');
        }
        return asset(ltrim($path, '/'));
    }
    return imageAsset('images/instructor/wenxin-portrait.png', 'images/instructor/wenxin-portrait.svg');
}

function instructorCredentialIconSvg(): array
{
    static $icons = [
        'degree' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"></path></svg>',
        'time' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l3 2"></path></svg>',
        'users' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'award' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.5 13.5L18 22l-6-3-6 3 2.5-8.5"></path></svg>',
    ];
    return $icons;
}

function instructorStatIconSvg(string $icon): string
{
    return match ($icon) {
        'courses' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"></path><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"></path></svg>',
        'star' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01z"></path></svg>',
        'video' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>',
        default => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    };
}
