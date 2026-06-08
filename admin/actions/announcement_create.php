<?php

require_once "../../config/init.php";

require_login();

require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$title =
    trim($_POST['title'] ?? '');

$message =
    trim($_POST['message'] ?? '');

$priority =
    trim($_POST['priority'] ?? 'info');

$start_date =
    !empty($_POST['start_date'])
    ? db_datetime($_POST['start_date'])
    : null;

$end_date =
    !empty($_POST['end_date'])
    ? db_datetime($_POST['end_date'])
    : null;

$active =
    isset($_POST['active'])
    ? 1
    : 0;

$created_by =
    $_SESSION['user_id'] ?? null;

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (
    empty($title) ||
    empty($message)
) {

    $_SESSION['error'] =
        'Popunite sva obavezna polja.';

    redirect('admin/announcements.php');
}

$allowedPriorities = [
    'info',
    'warning',
    'danger'
];

if (
    !in_array(
        $priority,
        $allowedPriorities,
        true
    )
) {

    $priority = 'info';
}

/*
|--------------------------------------------------------------------------
| INSERT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO announcements
    (
        title,
        message,
        priority,
        active,
        start_date,
        end_date,
        created_by
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?
    )
");

$stmt->bind_param(
    "sssissi",
    $title,
    $message,
    $priority,
    $active,
    $start_date,
    $end_date,
    $created_by
);

$stmt->execute();

/*
|--------------------------------------------------------------------------
| AUDIT
|--------------------------------------------------------------------------
*/

audit_log(
    'announcements',
    'create',
    $stmt->insert_id,
    'Kreirano obaveštenje: ' . $title
);

$_SESSION['success'] =
    'Obaveštenje je uspešno kreirano.';

redirect('admin/announcements.php');
