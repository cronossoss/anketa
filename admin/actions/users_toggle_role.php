<?php

ob_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../config/db.php";

require_once "../../helpers/auth.php";
require_once "../../helpers/csrf.php";

require_login();
require_admin();

header('Content-Type: application/json');

ob_clean();

if (!verify_csrf($_POST['csrf'] ?? '')) {

    echo json_encode([
        'status' => 'error'
    ]);

    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id === $_SESSION['user_id']) {

    echo json_encode([
        'status' => 'forbidden'
    ]);

    exit;
}

$stmt = $conn->prepare("
    SELECT role
    FROM users
    WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$user) {

    echo json_encode([
        'status' => 'not_found'
    ]);

    exit;
}

$newRole =
    strtolower($user['role']) === 'admin'
    ? 'user'
    : 'admin';

$stmt = $conn->prepare("
    UPDATE users
    SET role=?
    WHERE id=?
");

$stmt->bind_param(
    "si",
    $newRole,
    $id
);

$stmt->execute();

echo json_encode([
    'status' => 'ok',
    'role' => $newRole
]);
