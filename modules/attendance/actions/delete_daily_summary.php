<?php

require_once '../../../config/init.php';

$conn->query("
    DELETE FROM attendance_daily_summary
");

echo "Daily summary obrisan.";