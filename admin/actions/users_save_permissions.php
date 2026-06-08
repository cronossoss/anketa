<?php

require_once '../../config/init.php';

require_login();

if (!canAccessUsers()) {
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    exit('CSRF');
}

$userId =
    (int)($_POST['user_id'] ?? 0);

$permissions =
    $_POST['permissions'] ?? [];

$stmt = $conn->prepare("
    DELETE FROM user_permissions
    WHERE user_id = ?
");

$stmt->bind_param(
    'i',
    $userId
);

$stmt->execute();

$stmt = $conn->prepare("
    INSERT INTO user_permissions
    (
        user_id,
        permission
    )
    VALUES
    (
        ?,
        ?
    )
");

foreach ($permissions as $permission) {

    $stmt->bind_param(
        'is',
        $userId,
        $permission
    );

    $stmt->execute();
}

header(
    'Location: ' .
    url('admin/users.php')
);

exit;