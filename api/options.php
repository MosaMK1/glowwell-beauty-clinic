<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

try {
    $staffStmt = $pdo->query("SELECT s.staff_id, CONCAT(COALESCE(s.first_name, ''), ' ', COALESCE(s.last_name, '')) AS name, s.specialization
                              FROM staff s
                              ORDER BY s.first_name, s.last_name");
    $serviceStmt = $pdo->query("SELECT service_id, service_name, price, duration_minutes FROM services ORDER BY service_name");

    echo json_encode([
        'status' => 'success',
        'staff' => $staffStmt->fetchAll(),
        'services' => $serviceStmt->fetchAll(),
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to load booking options.']);
}
