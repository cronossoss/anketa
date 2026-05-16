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

$id = (int)($_POST['delete_id'] ?? 0);

$stmt = $conn->prepare("
    DELETE FROM users
    WHERE id=?
");

$stmt->bind_param("i", $id);

$stmt->execute();

audit_log(
    'users',
    'delete',
    $id,
    'Obrisan korisnik ID: ' .
        $id
);

redirect('admin/users.php');
