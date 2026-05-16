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

$stmt = $conn->prepare("
    UPDATE organizational_units
        SET
            type = ?,
            code = ?,
            od_code = ?,
            name = ?,
            description = ?
        WHERE id = ?
");

$stmt->bind_param(
    "sssssi",
    $type,
    $code,
    $od_code,
    $name,
    $description,
    $id,

);

$stmt->execute();

redirect('admin/organization.php');
exit;
