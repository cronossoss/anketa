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

$id =
    (int)$_POST['id'];


/* =========================
   DELETE RELATIONS
========================= */

$stmt = $conn->prepare("
    DELETE FROM organizational_relations
    WHERE
        parent_id = ?
        OR child_id = ?
");

$stmt->bind_param(
    "ii",
    $id,
    $id
);

$stmt->execute();


/* =========================
   DELETE UNIT
========================= */

$stmt = $conn->prepare("
    DELETE FROM organizational_units
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

redirect('admin/organization.php');
exit;
