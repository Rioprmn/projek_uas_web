<?php
session_start();

// Redirect based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: customer.php');
    }
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>
