<?php

require_once '../../../config/init.php';

$name =
    trim($_POST['name']);

$expectedStart =
    $_POST['expected_start'];

$expectedEnd =
    $_POST['expected_end'];

$lateTolerance =
    (int) $_POST['late_tolerance_minutes'];

$workDays =
    trim($_POST['work_days']);

$stmt = $conn->prepare("
    INSERT INTO attendance_work_schedules (

        name,
        expected_start,
        expected_end,
        late_tolerance_minutes,
        work_days

    )
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    'sssis',
    $name,
    $expectedStart,
    $expectedEnd,
    $lateTolerance,
    $workDays
);

$stmt->execute();

header('Location: ../schedules/index.php');

exit;
