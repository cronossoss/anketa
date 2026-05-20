<?php

require_once '../../../config/init.php';

$employeeId =
    (int) $_POST['employee_id'];

$absenceTypeId =
    (int) $_POST['absence_type_id'];

$dateFrom =
    str_replace(
        'T',
        ' ',
        $_POST['date_from']
    );

$dateTo =
    str_replace(
        'T',
        ' ',
        $_POST['date_to']
    );

$note =
    trim($_POST['note']);

$stmt = $conn->prepare("
    INSERT INTO attendance_absences (

        employee_id,
        absence_type_id,
        date_from,
        date_to,
        note

    )
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    'iisss',
    $employeeId,
    $absenceTypeId,
    $dateFrom,
    $dateTo,
    $note
);

$stmt->execute();

header('Location: ../absences/index.php');

exit;