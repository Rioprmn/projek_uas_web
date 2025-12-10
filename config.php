<?php
// Database configuration for init and future usage
// Adjust these values if your MySQL root password or host differs.
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pos_warung');
// Simple admin credential for demo purposes (change in production)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

function getPDO($withDB = true){
    $dsn = 'mysql:host=' . DB_HOST . ($withDB ? ';dbname=' . DB_NAME : '') . ';charset=utf8mb4';
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $opts);
}

?>
