<?php
require_once __DIR__ . '/../src/bootstrap.php';

$filename = $_GET['file'] ?? '';
$path = __DIR__ . '/../data/avatars/' . basename($filename);

if (!preg_match('/^[a-f0-9]{32}\.(jpg|png)$/', $filename)) {
    http_response_code(400);
    exit("Invalid filename");
}

if (!file_exists($path)) {
    http_response_code(404);
    exit("Not found");
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
header("Content-Type: $mime");
readfile($path);
