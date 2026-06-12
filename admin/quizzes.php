<?php
declare(strict_types=1);

$pageTitle = 'แบบทดสอบ';
require_once dirname(__DIR__) . '/includes/admin_header.php';
require_once dirname(__DIR__) . '/includes/quiz.php';

$message = flash('admin_success');
$filterCourse = (int) ($_GET['course_id'] ?? 0);
$quizId = (int) ($_GET['quiz_id'] ?? 0);
$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'save_quiz') {
        $id = (int) ($_POST['id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $passScore = max(1, min(100, (int) ($_POST['pass_score'] ?? 70)));
        $timeLimit = max(0, (int) ($_POST['time_limit_minutes'] ?? 0));
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $published = isset($_POST['is_published']) ? 1 : 0;
        if ($courseId && $title) {
            if ($id) {
                $stmt = db()->prepare('UPDATE quizzes SET course_id=?, title=?, description=?, pass_score=?, time_limit_minutes=?, sort_order=?, is_published=? WHERE id=?');
                $stmt->execute([$courseId, $title, $description ?: null, $passScore, $timeLimit, $sortOrder, $published, $id]);
            } else {
                $stmt = db()->prepare('INSERT INTO quizzes (course_id, title, description, pass_score, time_limit_minutes, sort_order, is_published) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$courseId, $title, $description ?: null, $passScore, $timeLimit, $sortOrder, $published]);
                $id = (int) db()->lastInsertId();
            }
            flash('admin_success', 'บันทึกแบบทดสอบเรียบร้อย');
            redirect('/admin/quizzes.php?action=questions&quiz_id=' . $id);
        }
    }

    if ($postAction === 'save_question') {
        $qid = (int) ($_POST['question_id'] ?? 0);
        $quizIdPost = (int) ($_POST['quiz_id'] ?? 0);
        $text = trim($_POST['question_text'] ?? '');
        $correct = trim($_POST['correct_key'] ?? 'A');
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $options = [];
        foreach (['A', 'B', 'C', 'D'] as $key) {
            $val = trim($_POST['option_' . $key] ?? '');
            if ($val !== '') {
                $options[$key] = $val;
            }
        }
        if ($quizIdPost && $text && $options) {
            $json = json_encode($options, JSON_UNESCAPED_UNICODE);
            if ($qid) {
                $stmt = db()->prepare('UPDATE quiz_questions SET question_text=?, options_json=?, correct_key=?, sort_order=? WHERE id=?');
                $stmt->execute([$text, $json, $correct, $sortOrder, $qid]);
            } else {
                $stmt = db()->prepare('INSERT INTO quiz_questions (quiz_id, question_text, options_json, correct_key, sort_order) VALUES (?,?,?,?,?)');
                $stmt->execute([$quizIdPost, $text, $json, $correct, $sortOrder]);
            }
            flash('admin_success', 'บันทึกคำถามเรียบร้อย');
        }
        redirect('/admin/quizzes.php?action=questions&quiz_id=' . $quizIdPost);
    }

    if ($postAction === 'delete_question') {
        $qid = (int) ($_POST['question_id'] ?? 0);
        $quizIdPost = (int) ($_POST['quiz_id'] ?? 0);
        if ($qid) {
            db()->prepare('DELETE FROM quiz_questions WHERE id = ?')->execute([$qid]);
            flash('admin_success', 'ลบคำถามแล้ว');
        }
        redirect('/admin/quizzes.php?action=questions&quiz_id=' . $quizIdPost);
    }

    if ($postAction === 'delete_quiz') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            db()->prepare('DELETE FROM quizzes WHERE id = ?')->execute([$id]);
            flash('admin_success', 'ลบแบบทดสอบแล้ว');
        }
        redirect('/admin/quizzes.php');
    }
}

$courses = getCourses(null, false);
$quizzes = [];
$sql = 'SELECT q.*, c.title AS course_title FROM quizzes q JOIN courses c ON c.id = q.course_id';
$params = [];
if ($filterCourse) {
    $sql .= ' WHERE q.course_id = ?';
    $params[] = $filterCourse;
}
$sql .= ' ORDER BY q.course_id, q.sort_order, q.id';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$quizzes = $stmt->fetchAll();

$editQuiz = $quizId && $action === 'edit' ? getQuizById($quizId) : null;
$manageQuiz = $quizId && $action === 'questions' ? getQuizById($quizId) : null;
$questions = $manageQuiz ? getQuizQuestions($quizId) : [];
$editQuestionId = (int) ($_GET['qid'] ?? 0);
$editQuestion = null;
if ($editQuestionId && $manageQuiz) {
    foreach ($questions as $q) {
        if ((int) $q['id'] === $editQuestionId) {
            $editQuestion = $q;
            break;
        }
    }
}
?>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<?php if ($action === 'add' || $editQuiz): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $editQuiz ? 'แก้ไขแบบทดสอบ' : 'เพิ่มแบบทดสอบ' ?></h2>
        <a href="<?= APP_URL ?>/admin/quizzes.php" class="btn btn-secondary btn-sm">กลับ</a>
    </div>
    <div class="admin-card-body">
        <form method="post">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_quiz">
            <?php if ($editQuiz): ?><input type="hidden" name="id" value="<?= (int) $editQuiz['id'] ?>"><?php endif; ?>
            <div class="form-group">
                <label>คอร์ส *</label>
                <select name="course_id" class="form-control" required>
                    <?php foreach ($courses as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (($editQuiz['course_id'] ?? $filterCourse) == $c['id']) ? 'selected' : '' ?>><?= e($c['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>ชื่อแบบทดสอบ *</label>
                <input type="text" name="title" class="form-control" required value="<?= e($editQuiz['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" class="form-control"><?= e($editQuiz['description'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>คะแนนผ่าน (%)</label>
                    <input type="number" name="pass_score" class="form-control" min="1" max="100" value="<?= (int) ($editQuiz['pass_score'] ?? 70) ?>">
                </div>
                <div class="form-group">
                    <label>จำกัดเวลา (นาที, 0=ไม่จำกัด)</label>
                    <input type="number" name="time_limit_minutes" class="form-control" min="0" value="<?= (int) ($editQuiz['time_limit_minutes'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label>ลำดับ</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= (int) ($editQuiz['sort_order'] ?? 0) ?>">
                </div>
            </div>
            <label><input type="checkbox" name="is_published" <?= ($editQuiz['is_published'] ?? 1) ? 'checked' : '' ?>> เผยแพร่</label>
            <div style="margin-top:1rem">
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<?php elseif ($manageQuiz): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2>คำถาม: <?= e($manageQuiz['title']) ?></h2>
        <a href="<?= APP_URL ?>/admin/quizzes.php" class="btn btn-secondary btn-sm">กลับ</a>
    </div>
    <div class="admin-card-body">
        <form method="post" style="margin-bottom:2rem;padding:1rem;background:#f9fafb;border-radius:8px">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_question">
            <input type="hidden" name="quiz_id" value="<?= (int) $quizId ?>">
            <?php if ($editQuestion): ?><input type="hidden" name="question_id" value="<?= (int) $editQuestion['id'] ?>"><?php endif; ?>
            <div class="form-group">
                <label>คำถาม *</label>
                <textarea name="question_text" class="form-control" required><?= e($editQuestion['question_text'] ?? '') ?></textarea>
            </div>
            <?php $opts = $editQuestion ? parseQuestionOptions($editQuestion) : []; ?>
            <?php foreach (['A', 'B', 'C', 'D'] as $key): ?>
            <div class="form-group">
                <label>ตัวเลือก <?= $key ?></label>
                <input type="text" name="option_<?= $key ?>" class="form-control" value="<?= e($opts[$key] ?? '') ?>">
            </div>
            <?php endforeach; ?>
            <div class="form-row">
                <div class="form-group">
                    <label>คำตอบที่ถูก</label>
                    <select name="correct_key" class="form-control">
                        <?php foreach (['A','B','C','D'] as $key): ?>
                        <option value="<?= $key ?>" <?= ($editQuestion['correct_key'] ?? 'A') === $key ? 'selected' : '' ?>><?= $key ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>ลำดับ</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= (int) ($editQuestion['sort_order'] ?? 0) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editQuestion ? 'อัปเดตคำถาม' : 'เพิ่มคำถาม' ?></button>
            <?php if ($editQuestion): ?>
            <a href="<?= APP_URL ?>/admin/quizzes.php?action=questions&quiz_id=<?= $quizId ?>" class="btn btn-secondary">ยกเลิกแก้ไข</a>
            <?php endif; ?>
        </form>

        <table class="data-table">
            <thead><tr><th>#</th><th>คำถาม</th><th>คำตอบ</th><th>จัดการ</th></tr></thead>
            <tbody>
                <?php foreach ($questions as $i => $q): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($q['question_text']) ?></td>
                    <td><?= e($q['correct_key']) ?></td>
                    <td>
                        <a href="<?= APP_URL ?>/admin/quizzes.php?action=questions&quiz_id=<?= $quizId ?>&qid=<?= (int) $q['id'] ?>" class="btn btn-secondary btn-sm">แก้ไข</a>
                        <form method="post" style="display:inline" onsubmit="return confirm('ลบคำถาม?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete_question">
                            <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                            <input type="hidden" name="question_id" value="<?= (int) $q['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!$questions): ?><p style="color:#6b7280">ยังไม่มีคำถาม</p><?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2>แบบทดสอบ (Quiz)</h2>
        <a href="<?= APP_URL ?>/admin/quizzes.php?action=add" class="btn btn-primary btn-sm">เพิ่มแบบทดสอบ</a>
    </div>
    <div class="admin-card-body">
        <form method="get" class="admin-inline-form" style="margin-bottom:1rem">
            <select name="course_id" class="form-control">
                <option value="0">ทุกคอร์ส</option>
                <?php foreach ($courses as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $filterCourse === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">กรอง</button>
        </form>
        <table class="data-table">
            <thead>
                <tr><th>คอร์ส</th><th>ชื่อ</th><th>ผ่าน</th><th>สถานะ</th><th>จัดการ</th></tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $q): ?>
                <tr>
                    <td><?= e($q['course_title']) ?></td>
                    <td><?= e($q['title']) ?></td>
                    <td><?= (int) $q['pass_score'] ?>%</td>
                    <td><?= $q['is_published'] ? 'เผยแพร่' : 'ซ่อน' ?></td>
                    <td style="display:flex;gap:.25rem;flex-wrap:wrap">
                        <a href="<?= APP_URL ?>/admin/quizzes.php?action=questions&quiz_id=<?= (int) $q['id'] ?>" class="btn btn-primary btn-sm">คำถาม</a>
                        <a href="<?= APP_URL ?>/admin/quizzes.php?action=edit&quiz_id=<?= (int) $q['id'] ?>" class="btn btn-secondary btn-sm">แก้ไข</a>
                        <form method="post" onsubmit="return confirm('ลบแบบทดสอบ?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete_quiz">
                            <input type="hidden" name="id" value="<?= (int) $q['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!$quizzes): ?><p style="color:#6b7280">ยังไม่มีแบบทดสอบ</p><?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
