<?php

require_once "../../config/db.php";

require_once "../../helpers/auth.php";

require_login();
require_role(['admin', 'it']);

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT
    e.*,
    ou.code AS unit_code,
    ou.name AS unit_name
FROM employees e

LEFT JOIN organizational_units ou
    ON ou.id = e.organizational_unit_id

WHERE e.id=?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$employee = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$employee) {

    echo json_encode([
        'status' => 'error'
    ]);

    exit;
}

echo json_encode([
    'status' => 'ok',
    'employee' => $employee
]);
