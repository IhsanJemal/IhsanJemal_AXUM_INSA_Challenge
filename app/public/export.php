<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$redis = $GLOBALS['redis'] ?? null;
if (!$redis) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Service unavailable']);
    exit;
}

// ---- Directories from environment (with safe defaults) ----
$logDir    = getenv('LOG_DIR') ?: __DIR__ . '/logs';
$uploadDir = getenv('UPLOAD_DIR') ?: __DIR__ . '/uploads';
$logFile   = $logDir . '/export.log';

// ---- Ensure directories exist and are writable ----
foreach ([$logDir, $uploadDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!is_writable($dir)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => "Directory not writable: {$dir}"]);
        exit;
    }
}

// ---- Audit logging helper ----
function audit_log(string $message): void {
    global $logFile;
    $entry = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
    if (file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX) === false) {
        error_log("Failed to write audit log: {$message}");
    }
}

// ---- Input validation ----
$key = $_GET['key'] ?? null;
if (!$key || !is_string($key)) {
    audit_log("EXPORT_FAIL invalid_key");
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// ---- Fetch data from Redis ----
$value = $redis->get($key);
if ($value === false || $value === null) {
    audit_log("EXPORT_MISS key={$key}");
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Key not found']);
    exit;
}

// ---- Write export file ----
$exportPath = $uploadDir . '/exported_' . date('Ymd_His') . '.txt';
if (file_put_contents($exportPath, $value) === false) {
    audit_log("EXPORT_FAIL key={$key} write_error");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to write export file']);
    exit;
}

// ---- Audit success ----
audit_log("EXPORT_OK key={$key} file=" . basename($exportPath));

// ---- Response ----
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'file'   => basename($exportPath),
    'path'   => $exportPath
]);

