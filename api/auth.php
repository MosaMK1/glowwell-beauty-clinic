<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth_check.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];

if ($action === 'login') {
    handleLogin($pdo, $data);
    exit;
}

if ($action === 'register') {
    handleRegister($pdo, $data);
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);

function handleLogin($pdo, $data) {
    $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = trim((string)($data['password'] ?? ''));

    if (!$email || $password === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email and password.']);
        return;
    }

    $sql = "SELECT u.user_id, u.email, u.password_hash, r.role_name, c.customer_id, s.staff_id,
                   COALESCE(c.first_name, s.first_name, 'Admin') AS first_name
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            LEFT JOIN customers c ON u.user_id = c.user_id
            LEFT JOIN staff s ON u.user_id = s.user_id
            WHERE u.email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['customer_id'] = $user['customer_id'] ?? null;
        $_SESSION['staff_id'] = $user['staff_id'] ?? null;

        $redirect = ($user['role_name'] === 'Admin') ? 'admin.php' : 'index.php';

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'redirect' => $redirect,
        ]);
        return;
    }

    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}

function handleRegister($pdo, $data) {
    $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = trim((string)($data['password'] ?? ''));
    $firstName = trim((string)($data['first_name'] ?? ''));
    $lastName = trim((string)($data['last_name'] ?? ''));
    $phone = trim((string)($data['phone'] ?? ''));

    if (!$email || strlen($password) < 6 || $firstName === '' || $lastName === '' || $phone === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All fields are required. Password must be at least 6 characters.']);
        return;
    }

    try {
        $pdo->beginTransaction();

        $check = $pdo->prepare('SELECT user_id FROM users WHERE email = :email');
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $pdo->rollBack();
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Email address is already registered.']);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userStmt = $pdo->prepare('INSERT INTO users (email, password_hash, role_id) VALUES (:email, :hash, 3)');
        $userStmt->execute([':email' => $email, ':hash' => $hash]);
        $userId = (int)$pdo->lastInsertId();

        $custStmt = $pdo->prepare('INSERT INTO customers (user_id, first_name, last_name, phone) VALUES (:user_id, :first_name, :last_name, :phone)');
        $custStmt->execute([
            ':user_id' => $userId,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':phone' => $phone,
        ]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! You can now log in.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Registration failed due to a server error.']);
    }
}
