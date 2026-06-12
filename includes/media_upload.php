<?php
declare(strict_types=1);

const MAX_COURSE_IMAGE_BYTES = 3 * 1024 * 1024;
const MAX_LESSON_DOC_BYTES = 10 * 1024 * 1024;

function ensureUploadDir(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function storeCourseCoverUpload(array $file): string|false|null
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        flash('admin_error', 'อัปโหลดรูปปกไม่สำเร็จ');
        return false;
    }
    if (($file['size'] ?? 0) > MAX_COURSE_IMAGE_BYTES) {
        flash('admin_error', 'รูปปกใหญ่เกิน 3MB');
        return false;
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        flash('admin_error', 'รูปปกรองรับเฉพาะ JPG, PNG, WEBP, GIF');
        return false;
    }
    ensureUploadDir(UPLOAD_COURSES_PATH);
    $filename = 'cover_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = UPLOAD_COURSES_PATH . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        flash('admin_error', 'บันทึกรูปปกไม่สำเร็จ');
        return false;
    }
    return 'uploads/courses/' . $filename;
}

function storeLessonDocumentUpload(array $file): string|false|null
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        flash('admin_error', 'อัปโหลดเอกสารไม่สำเร็จ');
        return false;
    }
    if (($file['size'] ?? 0) > MAX_LESSON_DOC_BYTES) {
        flash('admin_error', 'เอกสารใหญ่เกิน 10MB');
        return false;
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx'], true)) {
        flash('admin_error', 'เอกสารรองรับ PDF, DOC, DOCX, PPT, PPTX');
        return false;
    }
    ensureUploadDir(UPLOAD_COURSES_PATH);
    $filename = 'doc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = UPLOAD_COURSES_PATH . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        flash('admin_error', 'บันทึกเอกสารไม่สำเร็จ');
        return false;
    }
    return 'uploads/courses/' . $filename;
}

function mediaPublicUrl(string $storedPath): string
{
    if (str_starts_with($storedPath, 'http://') || str_starts_with($storedPath, 'https://')) {
        return $storedPath;
    }
    if (str_starts_with($storedPath, 'uploads/courses/')) {
        return APP_URL . '/public/download.php?file=' . urlencode(basename($storedPath));
    }
    return asset(ltrim($storedPath, '/'));
}
