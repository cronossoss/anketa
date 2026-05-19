<?php

require_once '../../../config/init.php';

$conn->query("
    DELETE FROM attendance_logs
    WHERE is_simulated = 1
");

echo "Fake logovi obrisani.";