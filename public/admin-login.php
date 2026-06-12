<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/rate_limit.php';

if (isAdminLoggedIn()) {
    redirect('/admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (isLoginRateLimited('admin', $username)) {
        $error = loginRateLimitMessage();
    } elseif ($username && $password && attemptLogin($username, $password)) {
        clearLoginAttempts('admin', $username);
        redirect('/admin/dashboard.php');
    } else {
        recordLoginFailure('admin', $username);
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}

$pageTitle = 'เข้าสู่ระบบ Admin';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-card">
        <img src="<?= e(brandLogoAsset()) ?>" alt="Logo" class="auth-logo">
        <h1>เข้าสู่ระบบ Admin</h1>
        <p style="text-align:center;color:var(--gray-600);margin-bottom:1.5rem;font-size:.9rem">สำหรับทีมงาน Wenxin Chinese</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrfField() ?>
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required autofocus autocomplete="username">
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
        </form>
        <p style="text-align:center;margin-top:1rem;font-size:.9rem">
            <a href="<?= APP_URL ?>/public/index.php">กลับหน้าแรก</a>
        </p>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
