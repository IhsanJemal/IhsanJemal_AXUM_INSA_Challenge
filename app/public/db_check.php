<?php
require_once __DIR__ . '/../src/bootstrap.php';

$stmt = $GLOBALS['pdo']->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Tables:\n";
foreach ($tables as $t) {
    echo "- $t\n";
}
