<?php
require_once __DIR__ . '/auth_check.php';
requireRole(['Admin']);

require_once __DIR__ . '/db.php';

$stmt = $pdo->query("SELECT a.appointment_id, c.full_name AS customer, s.full_name AS specialist, ser.name AS service_name, a.appt_date AS appointment_date, a.start_time, a.status
FROM appointments a
JOIN customers c ON a.customer_id = c.customer_id
JOIN staff s ON a.staff_id = s.staff_id
JOIN services ser ON a.service_id = ser.service_id
ORDER BY a.appt_date DESC, a.start_time DESC");
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowWell Admin Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 1100px;">
        <header class="topbar">
            <div>
                <h1>GlowWell Admin Portal</h1>
                <p class="subtitle">Appointment overview</p>
            </div>
            <div class="nav-actions">
                <a href="index.php" class="link-button">Customer Dashboard</a>
                <a href="logout.php" class="link-button danger">Logout</a>
            </div>
        </header>

        <div class="card">
            <h2>Booking Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Specialist</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$appointments): ?>
                        <tr><td colspan="7">No appointments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$row['appointment_id']) ?></td>
                                <td><?= htmlspecialchars($row['customer']) ?></td>
                                <td><?= htmlspecialchars($row['specialist']) ?></td>
                                <td><?= htmlspecialchars($row['service_name']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($row['start_time']) ?></td>
                                <td><span class="badge badge-primary"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
