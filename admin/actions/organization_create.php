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


/* =========================
   INSERT UNIT
========================= */

$stmt = $conn->prepare("
    INSERT INTO organizational_units
        (
            type,
            code,
            od_code,
            name,
            description
        )
        VALUES
        (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssss",
    $type,
    $code,
    $od_code,
    $name,
    $description

);

$stmt->execute();

redirect('admin/organization.php');
exit;
