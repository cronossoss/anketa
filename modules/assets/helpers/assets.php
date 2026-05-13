<?php

function getAssetTypes($conn)
{
    $sql = "
        SELECT
            id,
            name,
            code,
            created_at
        FROM asset_types
        ORDER BY name ASC
    ";

    $result = $conn->query($sql);

    $types = [];

    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $types[] = $row;
        }
    }

    return $types;
}

function getAssetTypeById(
    $conn,
    $id
) {

    $stmt = $conn->prepare("
        SELECT
            id,
            name,
            code,
            created_at
        FROM asset_types
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_assoc();
}
