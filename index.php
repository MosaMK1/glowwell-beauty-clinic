<?php
require_once __DIR__ . '/auth_check.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowWell Beauty Clinic - Online Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <header class="topbar">
            <div>
                <h1>GlowWell Beauty Clinic</h1>
                <p class="subtitle">Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</p>
            </div>
            <div class="nav-actions">
                <?php if (($_SESSION['role_name'] ?? '') === 'Admin'): ?>
                    <a href="admin.php" class="link-button">Admin Portal →</a>
                <?php endif; ?>
                <a href="logout.php" class="link-button danger">Logout</a>
            </div>
        </header>

        <div id="feedback" class="alert" style="display:none;"></div>

        <section>
            <h2>Book Your Appointment</h2>
            <form id="bookingForm">
                <input type="hidden" id="customer_id" value="<?= htmlspecialchars($_SESSION['customer_id'] ?? 1) ?>">

                <div class="form-group">
                    <label for="staff_id">Select Specialist</label>
                    <select id="staff_id" required>
                        <option value="">Loading specialists...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service_id">Select Treatment</label>
                    <select id="service_id" required>
                        <option value="">Loading treatments...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Preferred Date</label>
                    <input type="date" id="appointment_date" required>
                </div>

                <div class="form-group">
                    <label for="start_time">Time Slot</label>
                    <input type="time" id="start_time" required>
                </div>

                <button type="submit" class="btn primary">Confirm & Book Appointment</button>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('appointment_date').min = new Date().toISOString().split('T')[0];
            loadFormOptions();

            document.getElementById('bookingForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                const payload = {
                    customer_id: document.getElementById('customer_id').value,
                    staff_id: document.getElementById('staff_id').value,
                    service_id: document.getElementById('service_id').value,
                    appointment_date: document.getElementById('appointment_date').value,
                    start_time: document.getElementById('start_time').value,
                };

                try {
                    const res = await fetch('api/appointments.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();

                    if (res.ok) {
                        showAlert(data.message, 'success');
                        document.getElementById('bookingForm').reset();
                    } else {
                        showAlert(data.message, 'error');
                    }
                } catch (err) {
                    showAlert('Server connection error. Please try again.', 'error');
                }
            });
        });

        async function loadFormOptions() {
            try {
                const res = await fetch('api/options.php');
                const data = await res.json();

                if (data.status === 'success') {
                    const staffSelect = document.getElementById('staff_id');
                    staffSelect.innerHTML = '<option value="">-- Choose Specialist --</option>';
                    data.staff.forEach((staff) => {
                        staffSelect.innerHTML += `<option value="${staff.staff_id}">${escapeHtml(staff.name)} (${escapeHtml(staff.specialization)})</option>`;
                    });

                    const serviceSelect = document.getElementById('service_id');
                    serviceSelect.innerHTML = '<option value="">-- Choose Treatment --</option>';
                    data.services.forEach((service) => {
                        serviceSelect.innerHTML += `<option value="${service.service_id}">${escapeHtml(service.service_name)} (R${Number(service.price).toFixed(2)} - ${service.duration_minutes} min)</option>`;
                    });
                }
            } catch (err) {
                document.getElementById('staff_id').innerHTML = '<option value="1">Jane Doe (Skin Specialist)</option>';
                document.getElementById('service_id').innerHTML = `
                    <option value="1">HydraFacial Glow (R650.00 - 60 min)</option>
                    <option value="2">Deep Tissue Massage (R800.00 - 90 min)</option>
                    <option value="3">Keratin Hair Treatment (R1200.00 - 120 min)</option>
                `;
            }
        }

        function showAlert(msg, type) {
            const el = document.getElementById('feedback');
            el.className = `alert alert-${type}`;
            el.innerText = msg;
            el.style.display = 'block';
            setTimeout(() => { el.style.display = 'none'; }, 4000);
        }

        function escapeHtml(text) {
            return String(text).replace(/[&<>"']/g, (m) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m]));
        }
    </script>
</body>
</html>
