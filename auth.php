<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'status';

if ($action === 'login') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['role'] = 'admin';
        echo json_encode(['ok' => true, 'role' => 'admin']);
        exit;
    }
    echo json_encode(['ok' => false, 'msg' => 'Invalid credentials']);
    exit;
}

if ($action === 'logout') {
    unset($_SESSION['role']);
    echo json_encode(['ok' => true, 'role' => 'customer']);
    exit;
}

// status
echo json_encode(['ok' => true, 'role' => $_SESSION['role'] ?? 'customer']);
