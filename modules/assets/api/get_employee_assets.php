<?php

require_once __DIR__
    . '/../../../config/init.php';
    
require_login();

// require_role(['admin', 'it', 'hr']);

header('Content-Type: application/json');

$employeeId =
    (int)($_GET['employee_id'] ?? 0);

if ($employeeId <= 0) {

    echo json_encode([]);

    exit;
}

$stmt = $conn->prepare("
    SELECT
        a.id,

        a.inventory_number,

        a.manufacturer,
        a.model,

        c.name AS category_name,

        aa.assigned_at

    FROM asset_assignments aa

    LEFT JOIN assets a
        ON a.id = aa.asset_id

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    WHERE aa.employee_id = ?
    AND aa.returned_at IS NULL

    ORDER BY aa.assigned_at DESC
");

$stmt->bind_param(
    "i",
    $employeeId
);

$stmt->execute();

$result =
    $stmt
    ->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = $row;
}

echo json_encode($data);
