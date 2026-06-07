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


/* =========================
   INSERT UNIT
========================= */

$stmt = $conn->prepare("
    INSERT INTO organizational_units (

    type,
    code,
    od_code,
    name,
    description,
    manager_employee_id

)
        VALUES
        (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssi",
    $type,
    $code,
    $od_code,
    $name,
    $description,
    $manager_employee_id
);



$stmt->execute();

redirect('admin/organization.php');
exit;
