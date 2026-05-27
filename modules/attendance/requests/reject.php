<?php

require_once '../../../config/init.php';

require_login();

$id =
    (int) ($_GET['id'] ?? 0);

$userId =
    $_SESSION['user_id'];

$stmt = $conn->prepare("

    UPDATE attendance_absences

    SET

        status = 'rejected',

        approved_by = ?,

        approved_at = NOW()

    WHERE id = ?

");

$stmt->bind_param(
    'ii',
    $userId,
    $id
);

$stmt->execute();

$_SESSION['success'] =
    'Zahtev odbijen.';

header(
    'Location: index.php'
);

exit;
