:root {
    --primary: #bf4d8f;
    --primary-dark: #8d2f68;
    --success: #2c9c6d;
    --danger: #d74949;
    --text: #2a2a2a;
    --muted: #6b6b6b;
    --border: #e2dfe7;
    --bg: #f7f3f8;
    --card: #ffffff;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #fff9fc, #f4f0f7);
    color: var(--text);
}

.container {
    max-width: 980px;
    margin: 48px auto;
    padding: 24px;
}

.auth-container {
    max-width: 500px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(50, 20, 40, 0.08);
    padding: 30px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

h1, h2, h3 {
    margin: 0 0 8px;
}

.subtitle { color: var(--muted); margin: 0; }

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 18px;
}

label {
    font-weight: 600;
    margin-bottom: 8px;
}

input, select, button {
    font: inherit;
}

input, select {
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: white;
}

input:focus, select:focus {
    outline: 2px solid rgba(191, 77, 143, 0.2);
    border-color: var(--primary);
}

.btn, .link-button {
    display: inline-block;
    border: none;
    border-radius: 10px;
    padding: 12px 18px;
    text-decoration: none;
    cursor: pointer;
    transition: 0.2s ease;
    font-weight: 600;
}

.btn.primary, .link-button {
    background: var(--primary);
    color: white;
}

.btn.primary:hover, .link-button:hover {
    background: var(--primary-dark);
}

.link-button.danger {
    background: var(--danger);
}

.alert {
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-weight: 600;
}

.alert-success {
    background: rgba(44, 156, 109, 0.15);
    border: 1px solid rgba(44, 156, 109, 0.4);
    color: #165a3d;
}

.alert-error {
    background: rgba(215, 73, 73, 0.12);
    border: 1px solid rgba(215, 73, 73, 0.35);
    color: #8a2a2a;
}

.helper-text {
    text-align: center;
    margin-top: 18px;
    font-size: 0.95rem;
}

.helper-text a {
    color: var(--primary);
    text-decoration: none;
    font-weight: bold;
}

.info-box {
    background: #f5f1f6;
    padding: 12px 14px;
    border-radius: 10px;
    margin-top: 16px;
    color: var(--text);
    font-size: 0.9rem;
}

.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 10px 28px rgba(50, 20, 40, 0.06);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 18px;
}

th, td {
    padding: 12px 10px;
    border-bottom: 1px solid var(--border);
    text-align: left;
}

th {
    background: #faf5f8;
}

.badge {
    display: inline-block;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 0.8rem;
    font-weight: 700;
}

.badge-primary {
    background: rgba(191, 77, 143, 0.12);
    color: var(--primary-dark);
}

.nav-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

@media (max-width: 720px) {
    .container {
        padding: 16px;
        margin: 20px auto;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
    }

    table {
        display: block;
        overflow-x: auto;
    }
}
