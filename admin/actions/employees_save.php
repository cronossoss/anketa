<?php
require_once '../../config/db.php';

require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'])) {
    die('CSRF');
}

$name = trim($_POST['name']);
$surname = trim($_POST['surname']);

$stmt = $conn->prepare(
    "INSERT INTO employees (name, surname)
     VALUES (?, ?)"
);

$stmt->bind_param(
    'ss',
    $name,
    $surname
);

$stmt->execute();

$_SESSION['success'] = 'Zaposleni je sačuvan';

header('Location: ../employees.php');
exit;