<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/student_auth.php';

if (isStudentLoggedIn()) {
    redirect('/public/my-courses.php');
}

$success = flash('register_success');
$error = flash('register_error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    if ($password !== $confirm) {
        flash('register_error', 'รหัสผ่านไม่ตรงกัน');
        redirect('/public/register.php');
    }
    if (empty($_POST['accept_terms'])) {
        flash('register_error', 'กรุณายอมรับข้อกำหนดและนโยบายความเป็นส่วนตัว');
        redirect('/public/register.php');
    }
    $result = registerStudent(
        trim($_POST['full_name'] ?? ''),
        trim($_POST['student_phone'] ?? ''),
        $_POST['password'] ?? '',
        trim($_POST['email'] ?? '') ?: null,
        trim($_POST['line_id'] ?? '') ?: null
    );
    if ($result['ok']) {
        flash('register_success', $result['message']);
        redirect('/public/my-courses.php');
    }
    flash('register_error', $result['message']);
    redirect('/public/register.php');
}

$pageTitle = 'สมัครสมาชิก';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-card">
        <img src="<?= e(brandLogoAsset()) ?>" alt="Logo" class="auth-logo">
        <h1>สมัครสมาชิก</h1>
        <p style="text-align:center;color:var(--gray-600);margin-bottom:1.5rem;font-size:.9rem">สร้างบัญชีเพื่อเรียนและติดตามคอร์สของคุณ</p>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrfField() ?>
            <div class="form-group">
                <label>ชื่อ-นามสกุล *</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>เบอร์โทร *</label>
                <input type="tel" name="student_phone" class="form-control" required placeholder="ใช้สำหรับเข้าสู่ระบบ">
            </div>
            <div class="form-group">
                <label>รหัสผ่าน *</label>
                <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                <small style="color:var(--gray-500)">อย่างน้อย 6 ตัวอักษร</small>
            </div>
            <div class="form-group">
                <label>ยืนยันรหัสผ่าน *</label>
                <input type="password" name="password_confirm" class="form-control" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>อีเมล</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Line ID</label>
                <input type="text" name="line_id" class="form-control" placeholder="@username">
            </div>
            <div class="form-group">
                <label style="font-weight:400;display:flex;gap:.5rem;align-items:flex-start">
                    <input type="checkbox" name="accept_terms" value="1" required style="margin-top:.25rem">
                    <span>ฉันยอมรับ <a href="<?= APP_URL ?>/public/terms.php" target="_blank" rel="noopener">ข้อกำหนดการใช้งาน</a> และ <a href="<?= APP_URL ?>/public/privacy.php" target="_blank" rel="noopener">นโยบายความเป็นส่วนตัว</a></span>
                </label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
        </form>
        <p style="text-align:center;margin-top:1rem;font-size:.9rem">
            มีบัญชีแล้ว? <a href="<?= APP_URL ?>/public/login.php">เข้าสู่ระบบ</a>
        </p>
        <p style="text-align:center;margin-top:.5rem">
            <a href="<?= APP_URL ?>/public/index.php">กลับหน้าแรก</a>
        </p>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
