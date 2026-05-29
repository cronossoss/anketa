<?php

require_once '../../../config/init.php';

require_login();

$id =
    (int)($_GET['id'] ?? 0);

if (!$id) {

    header(
        'Location: index.php'
    );

    exit;
}

$stmt = $conn->prepare("

    UPDATE attendance_exit_passes

    SET status = 'rejected'

    WHERE id = ?

");

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$_SESSION['success'] =
    'Izlaznica odbijena.';

header(
    'Location: index.php'
);

exit;