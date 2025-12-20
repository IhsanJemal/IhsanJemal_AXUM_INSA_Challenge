<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/redis_client.php';

$redis = new RedisClient(getenv('REDIS_HOST') ?: 'redis', (int)(getenv('REDIS_PORT') ?: 6379));
$GLOBALS['redis'] = $redis;

try {
    $dbPath = '/var/www/html/data/app.db';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    $GLOBALS['pdo'] = $pdo;
} catch (Exception $e) {
    die("SQLite connection failed.");
}

// One-time schema init (safe if run repeatedly)
try {
    $GLOBALS['pdo']->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            display_name TEXT DEFAULT '',
            bio TEXT DEFAULT '',
            avatar_path TEXT DEFAULT ''
        );

        CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            owner TEXT NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX IF NOT EXISTS idx_notes_owner ON notes(owner);
    ");
} catch (Exception $e) {
    // Silent init failure is fine for demo visibility
}    