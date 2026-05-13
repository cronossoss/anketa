<?php

require_once "../../config/db.php";

require_once "../../helpers/auth.php";
require_once "../../helpers/csrf.php";
require_once "../../helpers/helpers.php";
require_once "../../helpers/audit.php";

require_login();
require_role(['admin', 'it']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}


/* =========================
   NORMALIZE USERNAME
========================= */

function normalize_username($string)
{
    $map = [
        'č' => 'c',
        'ć' => 'c',
        'ž' => 'z',
        'š' => 's',
        'đ' => 'dj',
        'Č' => 'c',
        'Ć' => 'c',
        'Ž' => 'z',
        'Š' => 's',
        'Đ' => 'dj'
    ];

    $string = strtr($string, $map);

    $string = strtolower($string);

    $string = preg_replace(
        '/[^a-z0-9\.]/',
        '',
        $string
    );

    return $string;
}


$emp_id =
    (int)($_POST['employee_id'] ?? 0);


/* =========================
   EMPLOYEE
========================= */

$stmt = $conn->prepare("
    SELECT *
    FROM employees
    WHERE id=?
");

$stmt->bind_param("i", $emp_id);

$stmt->execute();

$emp = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$emp) {
    exit;
}


/* =========================
   EXISTING USER
========================= */

$check = $conn->prepare("
    SELECT id
    FROM users
    WHERE employee_id=?
");

$check->bind_param("i", $emp_id);

$check->execute();

if (
    $check
    ->get_result()
    ->num_rows > 0
) {

    exit('User already exists');
}


/* =========================
   ACCOUNT ACCESS CHECK
========================= */

if (!(int)$emp['has_account']) {

    exit('Employee has no system access');
}


/* =========================
   USERNAME
========================= */

$base = normalize_username(
    $emp['first_name'] .
        "." .
        $emp['last_name']
);

$username = $base;

$i = 1;

while (true) {

    $check = $conn->prepare("
        SELECT id
        FROM users
        WHERE email=?
    ");

    $check->bind_param(
        "s",
        $username
    );

    $check->execute();

    if (
        $check
        ->get_result()
        ->num_rows === 0
    ) {
        break;
    }

    $username =
        $base . $i;

    $i++;
}


/* =========================
   PASSWORD
========================= */

$password = password_hash(
    $emp['personal_id'],
    PASSWORD_DEFAULT
);


/* =========================
   ROLE
========================= */

$role = 'user';


/* =========================
   INSERT
========================= */

$stmt = $conn->prepare("
    INSERT INTO users
    (
        email,
        password,
        employee_id,
        role
    )
    VALUES
    (?, ?, ?, ?)
");

$stmt->bind_param(
    "ssis",
    $username,
    $password,
    $emp_id,
    $role
);

$stmt->execute();

audit_log(
    'users',
    'create',
    $conn->insert_id,
    'Kreiran korisnik: ' .
        $username
);

header("Location: ../users.php");
exit;