<?php
declare(strict_types=1);

/**
 * CLI backup: php database/backup_cron.php
 * Saves to storage/backups/wenxin_lms_YYYY-mm-dd_His.sql
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/backup.php';

$dir = BASE_PATH . '/storage/backups';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$filename = $dir . '/wenxin_lms_' . date('Y-m-d_His') . '.sql';
$sql = exportDatabaseSql();
file_put_contents($filename, $sql);

$files = glob($dir . '/wenxin_lms_*.sql') ?: [];
usort($files, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
foreach (array_slice($files, 14) as $old) {
    @unlink($old);
}

echo "Backup saved: {$filename}\n";
