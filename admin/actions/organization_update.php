<?php

require_once "../../config/init.php";

require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'])) {
    die("CSRF");
}

$id =
    (int)$_POST['id'];

$type =
    trim($_POST['type']);

$code =
    trim($_POST['code']);

$name =
    trim($_POST['name']);

$description =
    trim($_POST['description']);

$od_code =
    trim($_POST['od_code']);

$managerEmployeeId =
    !empty($_POST['manager_employee_id'])
    ? (int)$_POST['manager_employee_id']
    : null;

$stmt = $conn->prepare("
    UPDATE organizational_units
        SET
            type = ?,
            code = ?,
            od_code = ?,
            name = ?,
            description = ?,
            manager_employee_id = ?
        WHERE id = ?
");

$stmt->bind_param(
    "sssssii",
    $type,
    $code,
    $od_code,
    $name,
    $description,
    $managerEmployeeId,
    $id
);

$stmt->execute();

redirect('admin/organization.php');
exit;
