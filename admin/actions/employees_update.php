<?php

require_once "../../config/db.php";

require_once "../../helpers/auth.php";
require_once "../../helpers/csrf.php";
require_once "../../helpers/helpers.php";
require_once "../../helpers/audit.php";

require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}


/* =========================
   DATA
========================= */

$id =
    (int)($_POST['employee_id'] ?? 0);

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

$contract_end =
    !empty($_POST['contract_end'])
    ? $_POST['contract_end']
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


/* =========================
   VALIDATION
========================= */

if (
    !$id ||
    empty($first_name) ||
    empty($last_name) ||
    !$organizational_unit_id
) {

    exit('Required fields missing');
}


/* =========================
   UPDATE
========================= */

$stmt = $conn->prepare("
    UPDATE employees
    SET
        first_name=?,
        last_name=?,
        organizational_unit_id=?,
        personal_id=?,
        position=?,
        photo=?,
        is_manager=?,
        email=?,
        jmbg=?,
        birth_date=?,
        hire_date=?,
        contract_end=?,
        contract_type=?,
        address=?,
        phone_private=?,
        bank_account=?,
        business_email=?,
        business_phone=?,
        has_account=?,
        system_role=?
    WHERE id=?
");

$stmt->bind_param(
    "ssisssisssssssssssisi",
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
    $contract_end,
    $contract_type,
    $address,
    $phone_private,
    $bank_account,
    $business_email,
    $business_phone,
    $has_account,
    $system_role,
    $id
);

$stmt->execute();

audit_log(
    'employees',
    'update',
    $id,
    'Izmenjen zaposleni: ' .
        $first_name . ' ' . $last_name
);


redirect(
    BASE_URL .
        'admin/employees.php'
);
