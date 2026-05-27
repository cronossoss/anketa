<?php

require_once '../../../config/init.php';

$conn->query("
    DELETE FROM attendance_logs
");

$conn->query("
    DELETE FROM attendance_daily_summary
");

$conn->query("
    DELETE FROM attendance_absences
");

$_SESSION['success'] =
    'Attendance podaci uspešno obrisani.';

header(
    'Location: ../dashboard.php'
);

exit;