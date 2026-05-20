<?php

require_once '../../../config/init.php';

$scheduleId =
    (int) $_POST['schedule_id'];

$employeeId =
    !empty($_POST['employee_id'])
        ? (int) $_POST['employee_id']
        : null;

$organizationalUnitId =
    !empty($_POST['organizational_unit_id'])
        ? (int) $_POST['organizational_unit_id']
        : null;

$validFrom =
    $_POST['valid_from'];

$stmt = $conn->prepare("
    INSERT INTO attendance_schedule_assignments (

        schedule_id,
        employee_id,
        organizational_unit_id,
        valid_from

    )
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    'iiis',
    $scheduleId,
    $employeeId,
    $organizationalUnitId,
    $validFrom
);

$stmt->execute();

header('Location: ../assignments/index.php');

exit;