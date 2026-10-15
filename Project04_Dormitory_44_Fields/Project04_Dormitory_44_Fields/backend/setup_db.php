<?php
require __DIR__.'/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();
$dbName = $_ENV['DB_DATABASE'] ?? 'dormitory_db';
if (!preg_match('/^[A-Za-z0-9_]+$/D', $dbName)) { fwrite(STDERR, "Invalid DB_DATABASE\n"); exit(1); }
try {
    $pdo = new PDO('mysql:host='.($_ENV['DB_HOST'] ?? '127.0.0.1').';port='.($_ENV['DB_PORT'] ?? '3306'),
        $_ENV['DB_USERNAME'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database ready.\n";
} catch (Throwable $e) { fwrite(STDERR, "Database setup failed: ".$e->getMessage()."\n"); exit(1); }
