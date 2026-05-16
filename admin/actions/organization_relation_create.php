<?php

require_once "../../config/init.php";

require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'])) {
    die("CSRF");
}

$parent_id =
    (int)$_POST['parent_id'];

$child_id =
    (int)$_POST['child_id'];

$parent_id = (int)$parent_id;
$child_id = (int)$child_id;


/* =========================
   LOAD UNITS
========================= */

$stmt = $conn->prepare("
    SELECT id, type
    FROM organizational_units
    WHERE id IN (?, ?)
");

$stmt->bind_param(
    "ii",
    $parent_id,
    $child_id
);

if (!$stmt->execute()) {

    die($stmt->error);
}

$result = $stmt->get_result();

$types = [];

while ($row = $result->fetch_assoc()) {

    $types[$row['id']] =
        $row['type'];
}


/* =========================
   VALIDATION
========================= */

if (
    $types[$parent_id] === 'OC'
    &&
    $types[$child_id] === 'OC'
) {

    $_SESSION['error'] =
        'OC ne može biti parent drugom OC.';

    redirect('admin/organization.php');
}

$relation_type =
    $_POST['relation_type'];

$od_code =
    trim($_POST['od_code']);

$stmt = $conn->prepare("
    INSERT INTO organizational_relations
    (
        parent_id,
        child_id,
        relation_type
    )
    VALUES
    (?, ?, ?)
");

$stmt->bind_param(
    "iis",
    $parent_id,
    $child_id,
    $relation_type
);

$stmt->execute();

redirect('admin/organization.php');
exit;
