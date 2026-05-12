<?php

require_once "../../config/db.php";

require_once "../../helpers/auth.php";
require_once "../../helpers/csrf.php";
require_once "../../helpers/helpers.php";

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

redirect(
    BASE_URL .
        'admin/users.php'
);
