<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_login();

require_role(['admin', 'it']);

if (!verify_csrf($_POST['csrf_token'])) {

    die('Neispravan CSRF token.');
}

$id = (int) $_POST['id'];

$category_id = (int) $_POST['category_id'];

$name = trim($_POST['name']);

$code = trim($_POST['code']);

$field_type = trim($_POST['field_type']);

$options = trim($_POST['options']);

$is_required =
    isset($_POST['is_required']) ? 1 : 0;

$sort_order =
    (int) $_POST['sort_order'];

$stmt = $conn->prepare("
    UPDATE asset_attribute_definitions
    SET
        category_id = ?,
        name = ?,
        code = ?,
        field_type = ?,
        options = ?,
        is_required = ?,
        sort_order = ?
    WHERE id = ?
");

$stmt->bind_param(
    "issssiii",
    $category_id,
    $name,
    $code,
    $field_type,
    $options,
    $is_required,
    $sort_order,
    $id
);

$stmt->execute();

$_SESSION['success'] =
    'Atribut je izmenjen.';

redirect(
    '../attributes/definitions.php'
);
