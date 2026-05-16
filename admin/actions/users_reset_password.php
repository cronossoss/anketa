<?php

require_once __DIR__ . '/../../config/init.php';

require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}

$id = (int)($_POST['reset_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT e.personal_id
    FROM users u
    JOIN employees e
        ON e.id = u.employee_id
    WHERE u.id=?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$emp = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$emp) {
    exit;
}

$newPass = password_hash(
    $emp['personal_id'],
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare("
    UPDATE users
    SET password=?
    WHERE id=?
");

$stmt->bind_param(
    "si",
    $newPass,
    $id
);

$stmt->execute();

audit_log(
    'users',
    'password_reset',
    $id,
    'Resetovana lozinka korisnika ID: ' .
        $id
);

redirect('admin/users.php');
