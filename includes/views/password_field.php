<?php

declare(strict_types=1);

$passwordName = $passwordName ?? 'password';
$passwordId = $passwordId ?? $passwordName;
$passwordValue = $passwordValue ?? '';
$passwordAttrs = $passwordAttrs ?? [];
?>
<div class="password-field">
    <input
        type="password"
        name="<?= e($passwordName) ?>"
        id="<?= e($passwordId) ?>"
        class="form-control"
        value="<?= e($passwordValue) ?>"
        <?php foreach ($passwordAttrs as $attr => $val): ?>
            <?php if ($val === true): ?>
                <?= e($attr) ?>
            <?php elseif ($val !== false && $val !== null): ?>
                <?= e($attr) ?>="<?= e((string) $val) ?>"
            <?php endif; ?>
        <?php endforeach; ?>
    >
    <button
        type="button"
        class="password-toggle"
        aria-label="แสดงรหัสผ่าน"
        aria-controls="<?= e($passwordId) ?>"
    >
        <svg class="password-toggle-icon password-toggle-icon--show" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        <svg class="password-toggle-icon password-toggle-icon--hide" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94"></path>
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19"></path>
            <path d="M1 1l22 22"></path>
            <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
        </svg>
    </button>
</div>
