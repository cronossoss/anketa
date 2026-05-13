<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_Login();

header('Content-Type: application/json');

$categoryId =
    (int)($_GET['category_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        code,
        field_type,
        options,
        is_required
    FROM asset_attribute_definitions
    WHERE category_id = ?
    ORDER BY sort_order, name
");

$stmt->bind_param(
    "i",
    $categoryId
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
