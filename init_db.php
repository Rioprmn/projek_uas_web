<?php
// Simple DB initializer. Run this from browser or CLI to create DB and sample data.
require_once __DIR__ . '/config.php';

try {
    // connect to server without DB specified
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database '".DB_NAME."' ensured.\n";

    // use DB and create tables
    $pdo = getPDO(true);

    $create = file_get_contents(__DIR__ . '/db_init.sql');
    // execute only statements after the CREATE DATABASE / USE lines
    // naive: split by ; and execute each
    $parts = preg_split('/;\s*\n/', $create);
    foreach ($parts as $part) {
        $sql = trim($part);
        if ($sql === '') continue;
        // skip CREATE DATABASE and USE when executing on connected DB
        if (stripos($sql, 'CREATE DATABASE') !== false) continue;
        if (stripos($sql, 'USE ') === 0) continue;
        $pdo->exec($sql);
    }

    echo "Tables created and sample data inserted.\n";
    echo "Done. You can open index.php in your browser.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
