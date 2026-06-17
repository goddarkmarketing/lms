<?php
declare(strict_types=1);
$pageTitle = 'ตั้งค่าเว็บไซต์';
require_once dirname(__DIR__) . '/includes/admin_header.php';

$message = flash('admin_success');
$keys = [
    'site_title', 'site_tagline', 'hero_title', 'hero_subtitle',
    'bank_account_name', 'bank_name', 'bank_account_number', 'payment_note',
    'facebook_url', 'line_id', 'phone', 'youtube_url', 'tiktok_url',
    'email_enabled', 'email_transport', 'email_from', 'email_from_name', 'email_admin', 'site_url',
    'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption',
    'line_notify_enabled', 'line_notify_token',
    'promptpay_enabled', 'promptpay_id', 'promptpay_id_type',
    'certificate_require_quiz',
    'privacy_policy_html', 'terms_html',
    'instructor_name', 'instructor_role', 'instructor_tagline', 'instructor_bio', 'instructor_quote',
    'instructor_photo', 'instructor_credentials', 'instructor_highlights',
    'instructor_stat_students', 'instructor_stat_satisfaction',
    'omise_enabled', 'omise_public_key', 'omise_secret_key',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        $stmt = db()->prepare('
            INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ');
        foreach ($keys as $key) {
            if (in_array($key, ['email_enabled', 'line_notify_enabled', 'promptpay_enabled', 'certificate_require_quiz', 'omise_enabled'], true)) {
                $value = isset($_POST[$key]) ? '1' : '0';
            } elseif (in_array($key, ['smtp_pass', 'omise_secret_key'], true) && trim($_POST[$key] ?? '') === '') {
                continue;
            } else {
                $value = trim($_POST[$key] ?? '');
            }
            $stmt->execute([$key, $value]);
        }
        flash('admin_success', 'บันทึกการตั้งค่าเรียบร้อย');
    } catch (Throwable $e) {
        flash('admin_error', 'เกิดข้อผิดพลาด');
    }
    redirect('/admin/settings.php');
}

$settings = getSettings();
?>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>ตั้งค่าหน้าเว็บและการชำระเงิน</h2>
        <a href="<?= APP_URL ?>/admin/content.php" class="btn btn-outline btn-sm">จัดการเนื้อหาเว็บ</a>
    </div>
    <div class="admin-card-body">
        <form method="post">
            <?= csrfField() ?>
            <h3>ข้อมูลเว็บไซต์</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>ชื่อเว็บ</label>
                    <input type="text" name="site_title" class="form-control" value="<?= e($settings['site_title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>สโลแกน</label>
                    <input type="text" name="site_tagline" class="form-control" value="<?= e($settings['site_tagline'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>หัวข้อ Hero</label>
                <input type="text" name="hero_title" class="form-control" value="<?= e($settings['hero_title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>คำอธิบาย Hero</label>
                <textarea name="hero_subtitle" class="form-control"><?= e($settings['hero_subtitle'] ?? '') ?></textarea>
            </div>

            <h3>ชำระเงิน</h3>
            <div class="form-group">
                <label>ชื่อบัญชี</label>
                <input type="text" name="bank_account_name" class="form-control" value="<?= e($settings['bank_account_name'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>ธนาคาร</label>
                    <input type="text" name="bank_name" class="form-control" value="<?= e($settings['bank_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>เลขบัญชี</label>
                    <input type="text" name="bank_account_number" class="form-control" value="<?= e($settings['bank_account_number'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>ข้อความแจ้งชำระ</label>
                <textarea name="payment_note" class="form-control"><?= e($settings['payment_note'] ?? '') ?></textarea>
            </div>

            <h3>ติดต่อ</h3>
            <div class="form-group">
                <label>Facebook URL</label>
                <input type="url" name="facebook_url" class="form-control" value="<?= e($settings['facebook_url'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Line ID</label>
                    <input type="text" name="line_id" class="form-control" value="<?= e($settings['line_id'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>เบอร์โทร</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($settings['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>YouTube URL</label>
                    <input type="url" name="youtube_url" class="form-control" value="<?= e($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@...">
                </div>
                <div class="form-group">
                    <label>TikTok URL</label>
                    <input type="url" name="tiktok_url" class="form-control" value="<?= e($settings['tiktok_url'] ?? '') ?>" placeholder="https://tiktok.com/@...">
                </div>
            </div>

            <h3>อีเมลแจ้งเตือน</h3>
            <div class="form-group">
                <label><input type="checkbox" name="email_enabled" value="1" <?= ($settings['email_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> เปิดใช้งานส่งอีเมล</label>
                <small style="display:block;color:#6b7280;margin-top:.25rem">ดู storage/logs/email.log หากส่งไม่สำเร็จ</small>
            </div>
            <div class="form-group">
                <label>วิธีส่งอีเมล</label>
                <select name="email_transport" class="form-control">
                    <option value="mail" <?= ($settings['email_transport'] ?? 'mail') === 'mail' ? 'selected' : '' ?>>PHP mail() (ค่าเริ่มต้น)</option>
                    <option value="smtp" <?= ($settings['email_transport'] ?? '') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>อีเมลผู้ส่ง (From)</label>
                    <input type="email" name="email_from" class="form-control" value="<?= e($settings['email_from'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>ชื่อผู้ส่ง</label>
                    <input type="text" name="email_from_name" class="form-control" value="<?= e($settings['email_from_name'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>อีเมลแจ้ง Admin (รับแจ้งชำระเงิน)</label>
                    <input type="email" name="email_admin" class="form-control" value="<?= e($settings['email_admin'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>URL เว็บไซต์ (สำหรับลิงก์ในอีเมล)</label>
                    <input type="url" name="site_url" class="form-control" value="<?= e($settings['site_url'] ?? 'http://localhost/LMS') ?>" placeholder="http://localhost/LMS">
                </div>
            </div>

            <h4 class="admin-subsection-title">ตั้งค่า SMTP</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control" value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                </div>
                <div class="form-group">
                    <label>SMTP Port</label>
                    <input type="number" name="smtp_port" class="form-control" value="<?= e($settings['smtp_port'] ?? '587') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SMTP User</label>
                    <input type="text" name="smtp_user" class="form-control" value="<?= e($settings['smtp_user'] ?? '') ?>" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>SMTP Password</label>
                    <input type="password" name="smtp_pass" class="form-control" placeholder="<?= ($settings['smtp_pass'] ?? '') !== '' ? '•••••••• (ว่างไว้ = ไม่เปลี่ยน)' : '' ?>" autocomplete="new-password">
                    <small style="color:#6b7280">หรือตั้งในไฟล์ .env เป็น SMTP_PASS</small>
                </div>
            </div>
            <div class="form-group">
                <label>การเข้ารหัส</label>
                <select name="smtp_encryption" class="form-control">
                    <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (พอร์ต 587)</option>
                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (พอร์ต 465)</option>
                    <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>ไม่เข้ารหัส</option>
                </select>
            </div>

            <h3>Line Notify</h3>
            <div class="form-group">
                <label><input type="checkbox" name="line_notify_enabled" value="1" <?= ($settings['line_notify_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> เปิดแจ้งเตือน Line Notify</label>
                <small style="display:block;color:#6b7280">สร้าง Token ที่ <a href="https://notify-bot.line.me/" target="_blank" rel="noopener">notify-bot.line.me</a></small>
            </div>
            <div class="form-group">
                <label>Line Notify Token</label>
                <input type="text" name="line_notify_token" class="form-control" value="<?= e($settings['line_notify_token'] ?? '') ?>" autocomplete="off">
            </div>

            <h3>PromptPay</h3>
            <div class="form-group">
                <label><input type="checkbox" name="promptpay_enabled" value="1" <?= ($settings['promptpay_enabled'] ?? '1') === '1' ? 'checked' : '' ?>> แสดง QR PromptPay หน้าชำระเงิน</label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>เบอร์/เลข PromptPay</label>
                    <input type="text" name="promptpay_id" class="form-control" value="<?= e($settings['promptpay_id'] ?? '') ?>" placeholder="ว่างไว้ใช้เบอร์โทรจากด้านล่าง">
                </div>
                <div class="form-group">
                    <label>ประเภท</label>
                    <select name="promptpay_id_type" class="form-control">
                        <option value="phone" <?= ($settings['promptpay_id_type'] ?? 'phone') === 'phone' ? 'selected' : '' ?>>เบอร์โทร</option>
                        <option value="national_id" <?= ($settings['promptpay_id_type'] ?? '') === 'national_id' ? 'selected' : '' ?>>เลขบัตรประชาชน</option>
                    </select>
                </div>
            </div>

            <h3>Omise ชำระเงินออนไลน์</h3>
            <div class="form-group">
                <label><input type="checkbox" name="omise_enabled" value="1" <?= ($settings['omise_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> เปิดชำระเงินออนไลน์ (PromptPay / บัตรเครดิต)</label>
                <small style="display:block;color:#6b7280;margin-top:.25rem">สมัครที่ <a href="https://www.omise.co/" target="_blank" rel="noopener">omise.co</a> — Webhook: <?= e((getSetting('site_url') ?: 'http://localhost/LMS') . '/public/omise_webhook.php') ?></small>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Omise Public Key (pkey_...)</label>
                    <input type="text" name="omise_public_key" class="form-control" value="<?= e($settings['omise_public_key'] ?? '') ?>" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Omise Secret Key (skey_...)</label>
                    <input type="password" name="omise_secret_key" class="form-control" placeholder="<?= ($settings['omise_secret_key'] ?? '') !== '' ? '•••••••• (ว่างไว้ = ไม่เปลี่ยน)' : '' ?>" autocomplete="new-password">
                    <small style="color:#6b7280">หรือตั้งใน .env เป็น OMISE_SECRET_KEY</small>
                </div>
            </div>

            <h3>ใบประกาศนียบัตร</h3>
            <div class="form-group">
                <label><input type="checkbox" name="certificate_require_quiz" value="1" <?= ($settings['certificate_require_quiz'] ?? '0') === '1' ? 'checked' : '' ?>> ต้องผ่านแบบทดสอบทุกชุดในคอร์สก่อนออกใบประกาศ</label>
                <small style="display:block;color:#6b7280;margin-top:.25rem">ถ้าคอร์สไม่มีแบบทดสอบ จะออกใบประกาศเมื่อเรียนครบ 100% ตามเดิม</small>
            </div>

            <h3>โปรไฟล์ผู้สอน</h3>
            <p style="color:#6b7280;font-size:.9rem;margin-bottom:1rem">แสดงที่หน้าแรกและ <a href="<?= APP_URL ?>/public/instructor.php" target="_blank" rel="noopener">หน้าโปรไฟล์ผู้สอน</a> — คredentials/แนวทางสอน คั่นด้วย |</p>
            <div class="form-row">
                <div class="form-group">
                    <label>ชื่อผู้สอน</label>
                    <input type="text" name="instructor_name" class="form-control" value="<?= e($settings['instructor_name'] ?? '') ?>" placeholder="อาจารย์เหวินซิน (Wenxin)">
                </div>
                <div class="form-group">
                    <label>ตำแหน่ง / บทบาท</label>
                    <input type="text" name="instructor_role" class="form-control" value="<?= e($settings['instructor_role'] ?? '') ?>" placeholder="ผู้สอนหลัก · Wenxin Chinese">
                </div>
            </div>
            <div class="form-group">
                <label>คำโปรยสั้น</label>
                <input type="text" name="instructor_tagline" class="form-control" value="<?= e($settings['instructor_tagline'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>คำคม / ข้อความหน้าแรก</label>
                <input type="text" name="instructor_quote" class="form-control" value="<?= e($settings['instructor_quote'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>ประวัติผู้สอน</label>
                <textarea name="instructor_bio" class="form-control" rows="4" placeholder="แยกย่อหน้าได้ด้วย Enter"><?= e($settings['instructor_bio'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>รูปโปรไฟล์ (path ใน assets หรือ URL)</label>
                <input type="text" name="instructor_photo" class="form-control" value="<?= e($settings['instructor_photo'] ?? '') ?>" placeholder="images/instructor/wenxin-portrait.png">
            </div>
            <div class="form-group">
                <label>ประวัติและความเชี่ยวชาญ (คั่นด้วย |)</label>
                <textarea name="instructor_credentials" class="form-control" rows="3" placeholder="ปริญญาโท...|ประสบการณ์..."><?= e($settings['instructor_credentials'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>แนวทางการสอน (คั่นด้วย |)</label>
                <textarea name="instructor_highlights" class="form-control" rows="3"><?= e($settings['instructor_highlights'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>สถิติผู้เรียน (แสดง)</label>
                    <input type="text" name="instructor_stat_students" class="form-control" value="<?= e($settings['instructor_stat_students'] ?? '') ?>" placeholder="5000+">
                </div>
                <div class="form-group">
                    <label>ความพึงพอใจ (แสดง)</label>
                    <input type="text" name="instructor_stat_satisfaction" class="form-control" value="<?= e($settings['instructor_stat_satisfaction'] ?? '') ?>" placeholder="95%">
                </div>
            </div>

            <h3>นโยบายและข้อกำหนด</h3>
            <p style="color:#6b7280;font-size:.9rem;margin-bottom:1rem">ว่างไว้จะใช้ข้อความมาตรฐาน — ใส่ HTML ได้ถ้าต้องการปรับแต่ง</p>
            <div class="form-group">
                <label>นโยบายความเป็นส่วนตัว (HTML)</label>
                <textarea name="privacy_policy_html" class="form-control" rows="4"><?= e($settings['privacy_policy_html'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>ข้อกำหนดการใช้งาน (HTML)</label>
                <textarea name="terms_html" class="form-control" rows="4"><?= e($settings['terms_html'] ?? '') ?></textarea>
            </div>

            <div class="admin-form-actions">
            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">บันทึกการตั้งค่า</button>
            </div>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
