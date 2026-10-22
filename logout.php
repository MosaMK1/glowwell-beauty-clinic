<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowWell - Customer Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-container" style="max-width: 520px;">
        <h2>Create Customer Account</h2>
        <p class="subtitle">Register to book online spa & beauty services</p>

        <div id="feedback" class="alert" style="display:none;"></div>

        <form id="registerForm">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" required placeholder="+27...">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password (Min 6 chars)</label>
                <input type="password" id="password" required minlength="6">
            </div>

            <button type="submit" class="btn primary">Register Account</button>
        </form>

        <p class="helper-text">
            Already registered? <a href="login.php">Log In Here</a>
        </p>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };

            try {
                const res = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (res.ok) {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = 'login.php', 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (err) {
                showAlert('Registration service error.', 'error');
            }
        });

        function showAlert(msg, type) {
            const el = document.getElementById('feedback');
            el.className = `alert alert-${type}`;
            el.innerText = msg;
            el.style.display = 'block';
        }
    </script>
</body>
</html>
