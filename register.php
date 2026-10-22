<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowWell - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-container">
        <h2>GlowWell Clinic Login</h2>
        <p class="subtitle">Sign in to manage your appointments</p>

        <div id="feedback" class="alert" style="display:none;"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required placeholder="admin@glowwell.co.za">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" required>
            </div>

            <button type="submit" class="btn primary">Log In</button>
        </form>

        <p class="helper-text">
            New customer? <a href="register.php">Create an account</a>
        </p>

        <div class="info-box">
            <strong>Default Test Credentials:</strong><br>
            Admin: <code>admin@glowwell.co.za</code> / <code>Admin@123</code><br>
            Customer: <code>thandi@example.com</code> / <code>Customer@123</code>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };

            try {
                const res = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (res.ok) {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = data.redirect, 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (err) {
                showAlert('Network or server error.', 'error');
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
