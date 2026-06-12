<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/student_auth.php';
require_once dirname(__DIR__) . '/includes/student_account.php';
require_once dirname(__DIR__) . '/includes/checkout_flow.php';
require_once dirname(__DIR__) . '/includes/progress.php';
require_once dirname(__DIR__) . '/includes/certificate.php';
require_once dirname(__DIR__) . '/includes/quiz.php';

requireStudentLogin('/public/profile.php');

$student = currentStudent();
if (!$student) {
    redirect('/public/login.php');
}

$tab = studentAccountTab($_GET['tab'] ?? 'courses');
$accountTabs = studentAccountTabs();
$message = flash('profile_success') ?? flash('payment_success') ?? '';
$error = flash('profile_error') ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $result = updateStudentProfile(
            (int) $student['id'],
            trim($_POST['full_name'] ?? ''),
            trim($_POST['email'] ?? '') ?: null,
            trim($_POST['line_id'] ?? '') ?: null
        );
        if ($result['ok']) {
            flash('profile_success', $result['message']);
        } else {
            flash('profile_error', $result['message']);
        }
        redirect('/public/profile.php?tab=profile');
    }

    if ($action === 'password') {
        $result = changeStudentPassword(
            (int) $student['id'],
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? ''
        );
        if ($result['ok']) {
            flash('profile_success', $result['message']);
        } else {
            flash('profile_error', $result['message']);
        }
        redirect('/public/profile.php?tab=password');
    }
}

$student = currentStudent();
$studentId = (int) $student['id'];
$studentInitial = studentAccountInitial($student['full_name'] ?? '');

syncEnrollmentsFromPaymentsForStudent($studentId);
$enrolled = getEnrolledCoursesByStudentId($studentId);
$courseIds = array_map(static fn($c) => (int) $c['id'], $enrolled);
$progressMap = getCourseProgressForStudent($studentId, $courseIds);
$certificates = getStudentCertificates($studentId);
$certByCourse = [];
foreach ($certificates as $cert) {
    $certByCourse[(int) $cert['course_id']] = $cert;
}
$activeCourseCount = count(array_filter($enrolled, static fn($c) => ($c['status'] ?? 'active') !== 'pending'));
$pendingCourseCount = count($enrolled) - $activeCourseCount;

$pageTitle = $accountTabs[$tab]['title'];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="account-page">
    <div class="container account-layout">
        <aside class="account-sidebar" aria-label="เมนูบัญชีผู้เรียน">
            <div class="account-sidebar-user">
                <div class="account-sidebar-avatar" aria-hidden="true"><?= e($studentInitial) ?></div>
                <div class="account-sidebar-user-text">
                    <strong><?= e($student['full_name']) ?></strong>
                    <?php if (!empty($student['email'])): ?>
                    <span><?= e($student['email']) ?></span>
                    <?php elseif (!empty($student['phone'])): ?>
                    <span><?= e($student['phone']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <nav class="account-nav">
                <?php foreach ($accountTabs as $tabKey => $tabInfo): ?>
                <a
                    href="<?= e(studentAccountUrl($tabKey)) ?>"
                    class="account-nav-link<?= $tab === $tabKey ? ' is-active' : '' ?>"
                    <?= $tab === $tabKey ? 'aria-current="page"' : '' ?>
                ><?= e($tabInfo['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="account-sidebar-footer">
                <a href="<?= APP_URL ?>/public/student-logout.php" class="account-nav-link account-nav-link--muted">ออกจากระบบ</a>
            </div>
        </aside>

        <div class="account-content">
            <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

            <?php if ($tab === 'courses'): ?>
            <div class="account-panel">
                <?php require dirname(__DIR__) . '/includes/views/account_tab_courses.php'; ?>
            </div>

            <?php elseif ($tab === 'profile'): ?>
            <div class="account-panel">
                <div class="account-panel-head">
                    <div>
                        <h1>ข้อมูลส่วนตัว</h1>
                        <p class="account-panel-desc">แก้ไขชื่อ อีเมล และ Line ID ของคุณ</p>
                    </div>
                </div>
                <div class="account-panel-card">
                    <form method="post">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="profile">
                        <div class="form-group">
                            <label>ชื่อ-นามสกุล</label>
                            <input type="text" name="full_name" class="form-control" required value="<?= e($student['full_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>เบอร์โทร</label>
                            <input type="tel" class="form-control" value="<?= e($student['phone'] ?? '') ?>" disabled>
                            <small class="form-hint">ใช้สำหรับเข้าสู่ระบบ — ติดต่อทีมงานหากต้องการเปลี่ยน</small>
                        </div>
                        <div class="form-group">
                            <label>อีเมล</label>
                            <input type="email" name="email" class="form-control" value="<?= e($student['email'] ?? '') ?>" placeholder="ใช้รับการแจ้งเตือนและรีเซ็ตรหัสผ่าน">
                        </div>
                        <div class="form-group">
                            <label>Line ID</label>
                            <input type="text" name="line_id" class="form-control" value="<?= e($student['line_id'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </form>
                </div>
            </div>

            <?php elseif ($tab === 'password'): ?>
            <div class="account-panel">
                <div class="account-panel-head">
                    <div>
                        <h1>เปลี่ยนรหัสผ่าน</h1>
                        <p class="account-panel-desc">อัปเดตรหัสผ่านเพื่อความปลอดภัยของบัญชี</p>
                    </div>
                </div>
                <div class="account-panel-card">
                    <form method="post">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="password">
                        <div class="form-group">
                            <label>รหัสผ่านปัจจุบัน</label>
                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่านใหม่</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                    </form>
                    <p class="account-panel-footnote">
                        <a href="<?= APP_URL ?>/public/forgot-password.php">ลืมรหัสผ่าน?</a>
                    </p>
                </div>
            </div>

            <?php else: ?>
            <div class="account-panel">
                <div class="account-panel-head">
                    <div>
                        <h1>ใบประกาศนียบัตร</h1>
                        <p class="account-panel-desc">ใบประกาศที่คุณได้รับจากการเรียนจบคอร์ส</p>
                    </div>
                </div>
                <div class="account-panel-card">
                    <?php if (!$certificates): ?>
                    <p class="account-empty-text">ยังไม่มีใบประกาศนียบัตร</p>
                    <?php else: ?>
                    <ul class="account-cert-list">
                        <?php foreach ($certificates as $cert): ?>
                        <li class="account-cert-item">
                            <div>
                                <strong><?= e($cert['course_title'] ?? '') ?></strong>
                                <small>ออกเมื่อ <?= e(date('d/m/Y', strtotime($cert['issued_at'] ?? 'now'))) ?></small>
                            </div>
                            <a href="<?= APP_URL ?>/public/certificate.php?code=<?= urlencode($cert['certificate_code'] ?? '') ?>" class="btn btn-outline btn-sm">ดูใบประกาศ</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
