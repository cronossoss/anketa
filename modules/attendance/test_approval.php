<?php

require_once '../../config/init.php';
require_once 'helpers/approval.php';

$employeeId = (int)($_GET['employee_id'] ?? 0);

echo '<h3>Employee ID: ' . $employeeId . '</h3>';

echo '<pre>';

var_dump(
    getApproverEmployeeId(
        $conn,
        $employeeId
    )
);
