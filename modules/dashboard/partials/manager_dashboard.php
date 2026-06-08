<?php

require_once ROOT_PATH . '/helpers/organization_tree.php';

$employeeId =
    $_SESSION['employee_id'];

$stmt = $conn->prepare("
    SELECT
        first_name,
        last_name,
        annual_leave_days
    FROM employees
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $employeeId
);

$stmt->execute();

$employee =
    $stmt
    ->get_result()
    ->fetch_assoc();

$employeeCount =
    getManagedEmployeesCount(
        $conn,
        $employeeId
    );

$stmt = $conn->prepare("
    SELECT name
    FROM organizational_units
    WHERE manager_employee_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $employeeId
);

$stmt->execute();

$unit =
    $stmt
    ->get_result()
    ->fetch_assoc();

$unitName =
    $unit['name']
    ?? 'Nije definisano';

?>

<div class="page-card">

    <div class="mb-4">

        <h3 class="mb-1">

            Dobrodošli,
            <?= e($employee['first_name']) ?>

        </h3>

        <div class="text-muted">

            Pregled organizacione jedinice

        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-people"></i>

                    Zaposlenih

                </div>

                <div class="dashboard-card-value">

                    <?= $employeeCount ?>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-calendar-check"></i>

                    Godišnji odmor

                </div>

                <div class="dashboard-card-value">

                    <?= (int)$employee['annual_leave_days'] ?>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-door-open"></i>

                    Izlaznice

                </div>

                <div class="dashboard-card-value">

                    0 / 8h

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-send-check"></i>

                    Zahtevi

                </div>

                <div class="dashboard-card-value">

                    0

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <strong>

                Moja organizaciona jedinica

            </strong>

        </div>

        <div class="card-body">

            <h5>

                <?= e($unitName) ?>

            </h5>

            <p class="mb-0">

                Broj zaposlenih:
                <strong>

                    <?= $employeeCount ?>

                </strong>

            </p>

        </div>

    </div>

    <div class="row g-3">

        <div class="col-md-3">

            <a
                href="<?= url('modules/attendance/my_ou/index.php') ?>"
                class="btn btn-primary w-100">

                Moja OJ

            </a>

        </div>

        <div class="col-md-3">

            <a
                href="<?= url('modules/attendance/approval_requests/index.php') ?>"
                class="btn btn-primary w-100">

                Zahtevi zaposlenih

            </a>

        </div>

        <div class="col-md-3">

            <a
                href="<?= url('modules/attendance/my_attendance/index.php') ?>"
                class="btn btn-primary w-100">

                Moje prisustvo

            </a>

        </div>

        <div class="col-md-3">

            <a
                href="<?= url('modules/attendance/requests/index.php') ?>"
                class="btn btn-primary w-100">

                Moji zahtevi

            </a>

        </div>

    </div>

</div>