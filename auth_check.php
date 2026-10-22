<?php
session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireRole($allowedRoles = []) {
    requireLogin();
    if (!in_array($_SESSION['role_name'] ?? '', $allowedRoles, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>403 Forbidden</title><link rel="stylesheet" href="style.css"></head><body>';
        echo '<div class="container" style="max-width: 500px; margin-top: 50px; text-align: center;">';
        echo '<h2>403 - Access Denied</h2>';
        echo '<p>You do not have permission to access this page.</p>';
        echo '<a href="index.php" class="btn primary">Return to dashboard</a>';
        echo '</div></body></html>';
        exit;
    }
}
