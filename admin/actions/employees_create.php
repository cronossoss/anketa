<?php

require_once "../../config/init.php";

require_login();
require_role(['admin', 'hr']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}


/* =========================
   DATA
========================= */

$first_name =
    trim($_POST['first_name'] ?? '');

$last_name =
    trim($_POST['last_name'] ?? '');

$organizational_unit_id =
    (int)($_POST['organizational_unit_id'] ?? 0);

$position =
    trim($_POST['position'] ?? '');

$email =
    trim($_POST['email'] ?? '');

$personal_id =
    trim($_POST['personal_id'] ?? '');

$jmbg =
    trim($_POST['jmbg'] ?? '');

$birth_date =
    !empty($_POST['birth_date'])
    ? $_POST['birth_date']
    : null;

$hire_date =
    !empty($_POST['hire_date'])
    ? $_POST['hire_date']
    : null;

$contract_type =
    trim($_POST['contract_type'] ?? '');

$address =
    trim($_POST['address'] ?? '');

$phone_private =
    trim($_POST['phone_private'] ?? '');

$bank_account =
    trim($_POST['bank_account'] ?? '');

$business_email =
    trim($_POST['business_email'] ?? '');

$business_phone =
    trim($_POST['business_phone'] ?? '');

$is_manager =
    isset($_POST['is_manager'])
    ? 1
    : 0;
$has_account =
    isset($_POST['has_account'])
    ? 1
    : 0;

$system_role =
    trim($_POST['system_role'] ?? '');

if (!$has_account) {

    $system_role = null;
}

$photo =
    !empty($personal_id)
    ? $personal_id . '.jpg'
    : null;

$contract_end =
    !empty($_POST['contract_end'])
    ? $_POST['contract_end']
    : null;


/* =========================
   VALIDATION
========================= */

if (
    empty($first_name) ||
    empty($last_name) ||
    !$organizational_unit_id
) {

    exit('Required fields missing');
}


/* =========================
   INSERT
========================= */

$stmt = $conn->prepare("
    INSERT INTO employees
    (
        first_name,
        last_name,
        organizational_unit_id,
        personal_id,
        position,
        photo,
        is_manager,
        email,
        jmbg,
        birth_date,
        hire_date,
        contract_type,
        address,
        phone_private,
        bank_account,
        business_email,
        business_phone,
        contract_end,
        has_account,
        system_role
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssisssisssssssssssis",
    $first_name,
    $last_name,
    $organizational_unit_id,
    $personal_id,
    $position,
    $photo,
    $is_manager,
    $email,
    $jmbg,
    $birth_date,
    $hire_date,
    $contract_type,
    $address,
    $phone_private,
    $bank_account,
    $business_email,
    $business_phone,
    $contract_end,
    $has_account,
    $system_role
);

$stmt->execute();

audit_log(
    'employees',
    'create',
    $conn->insert_id,
    'Kreiran zaposleni: ' .
        $first_name . ' ' . $last_name
);


redirect('admin/employees.php');
