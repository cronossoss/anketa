<?php

require_once '../../config/init.php';

require_login();

if (!canAccessUsers()) {
    exit;
}

$userId = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT permission
    FROM user_permissions
    WHERE user_id = ?
");

$stmt->bind_param(
    'i',
    $userId
);

$stmt->execute();

$result =
    $stmt->get_result();

$permissions = [];

while ($row = $result->fetch_assoc()) {

    $permissions[] =
        $row['permission'];
}

header('Content-Type: application/json');

echo json_encode([
    'permissions' => $permissions
]);