<?php

require_once '../../../config/init.php';

$name =
    trim($_POST['name']);

$code =
    trim($_POST['code']);

$paid =
    isset($_POST['paid']) ? 1 : 0;

$active =
    isset($_POST['active']) ? 1 : 0;

$stmt = $conn->prepare("
    INSERT INTO attendance_absence_types (

        name,
        code,
        paid,
        active

    )
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    'ssii',
    $name,
    $code,
    $paid,
    $active
);

$stmt->execute();

header('Location: ../absence-types/index.php');

exit;