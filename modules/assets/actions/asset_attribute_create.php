<?php

require_once "../../../config/init.php";

require_login();

require_role(['admin', 'it']);

if (
    !verify_csrf(
        $_POST['csrf_token'] ?? ''
    )
) {

    die('CSRF greška.');
}

$categoryId =
    (int)($_POST['category_id'] ?? 0);

$name =
    trim($_POST['name'] ?? '');

$code =
    trim($_POST['code'] ?? '');

$fieldType =
    trim($_POST['field_type'] ?? '');

$options =
    trim($_POST['options'] ?? '');

$sortOrder =
    (int)($_POST['sort_order'] ?? 0);

$isRequired =
    isset($_POST['is_required'])
    ? 1
    : 0;

$stmt = $conn->prepare("
    INSERT INTO asset_attribute_definitions (
        category_id,
        name,
        code,
        field_type,
        options,
        is_required,
        sort_order
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "issssii",
    $categoryId,
    $name,
    $code,
    $fieldType,
    $options,
    $isRequired,
    $sortOrder
);

$stmt->execute();

header(
    "Location: ../attributes/definitions.php"
);

exit;
