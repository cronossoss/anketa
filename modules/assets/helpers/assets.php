<?php

function getAssetById(
    mysqli $conn,
    int $id
): ?array {

    $stmt = $conn->prepare("
        SELECT
            a.*,
            t.name AS type_name,
            c.name AS category_name
        FROM assets a
        LEFT JOIN asset_types t
            ON t.id = a.asset_type_id
        LEFT JOIN asset_categories c
            ON c.id = a.category_id
        WHERE a.id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'i',
        $id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $asset = $result->fetch_assoc();

    return $asset ?: null;
}