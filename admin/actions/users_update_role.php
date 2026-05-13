<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../config/db.php";

require_once "../../helpers/auth.php";
require_once "../../helpers/csrf.php";

require_login();
require_role(['admin']);


/* =========================
   JSON
========================= */

$data = json_decode(
    file_get_contents("php://input"),
    true
);

echo "<pre>";
print_r($data);
echo "</pre>";


$user_id = (int)($data['user_id'] ?? 0);

$role = trim($data['role'] ?? '');


/* =========================
   CSRF
========================= */

if (!verify_csrf($data['csrf'] ?? '')) {
    exit('CSRF');
}


/* =========================
   UPDATE
========================= */

$stmt = $conn->prepare("
    UPDATE users
    SET role=?
    WHERE id=?
");

$stmt->bind_param(
    "si",
    $role,
    $user_id
);

$stmt->execute();

echo 'OK';