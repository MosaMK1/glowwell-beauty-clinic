<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$customerId = (int)($data['customer_id'] ?? 0);
$staffId = (int)($data['staff_id'] ?? 0);
$serviceId = (int)($data['service_id'] ?? 0);
$appointmentDate = trim((string)($data['appointment_date'] ?? ''));
$startTime = trim((string)($data['start_time'] ?? ''));

if ($customerId <= 0 || $staffId <= 0 || $serviceId <= 0 || $appointmentDate === '' || $startTime === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required booking details.']);
    exit;
}

try {
    $serviceCheck = $pdo->prepare('SELECT service_id, duration_min, price FROM services WHERE service_id = :id');
    $serviceCheck->execute([':id' => $serviceId]);
    $service = $serviceCheck->fetch();

    if (!$service) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Selected treatment is invalid.']);
        exit;
    }

    $staffCheck = $pdo->prepare('SELECT staff_id FROM staff WHERE staff_id = :id');
    $staffCheck->execute([':id' => $staffId]);
    if (!$staffCheck->fetch()) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Selected specialist is invalid.']);
        exit;
    }

    $conflict = $pdo->prepare("SELECT appointment_id FROM appointments WHERE staff_id = :staff_id AND appt_date = :date AND start_time = :time AND status != 'cancelled'");
    $conflict->execute([
        ':staff_id' => $staffId,
        ':date' => $appointmentDate,
        ':time' => $startTime,
    ]);

    if ($conflict->fetch()) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'This time slot is already booked for the selected specialist.']);
        exit;
    }

    $endTime = $startTime;

    $insert = $pdo->prepare('INSERT INTO appointments (customer_id, staff_id, service_id, appt_date, start_time, end_time, status, price_charged, created_at) VALUES (:customer_id, :staff_id, :service_id, :appt_date, :start_time, :end_time, :status, :price_charged, NOW())');
    $insert->execute([
        ':customer_id' => $customerId,
        ':staff_id' => $staffId,
        ':service_id' => $serviceId,
        ':appt_date' => $appointmentDate,
        ':start_time' => $startTime,
        ':end_time' => $endTime,
        ':status' => 'pending',
        ':price_charged' => (float)$service['price'],
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Appointment booked successfully.',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Booking failed due to a server error.']);
}
