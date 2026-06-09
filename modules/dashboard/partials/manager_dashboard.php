<?php

require_once ROOT_PATH . '/helpers/organization_tree.php';

$employeeId =
    $_SESSION['employee_id'];

$employeeCount =
    getManagedEmployeesCount(
        $conn,
        $employeeId
    );

$stmt = $conn->prepare("
    SELECT
        name
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
    ?? 'Nedefinisano';

$pendingRequests = 0;
$newDocuments = 0;
$reminders = 0;
$urgentAnnouncements = 0;
?>

<div class="row g-3 mb-4">

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                <i class="bi bi-check2-square"></i>

                Zahtevi za odobrenje

            </div>

            <div class="dashboard-card-value">

                <?= $pendingRequests ?>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                <i class="bi bi-folder2-open"></i>

                Dokumenta

            </div>

            <div class="dashboard-card-value">

                <?= $newDocuments ?>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                <i class="bi bi-bell"></i>

                Podsetnici

            </div>

            <div class="dashboard-card-value">

                <?= $reminders ?>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                <i class="bi bi-megaphone"></i>

                Hitna obaveštenja

            </div>

            <div class="dashboard-card-value">

                <?= $urgentAnnouncements ?>

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

        <h4>

            <?= e($unitName) ?>

        </h4>

        <div class="row mt-3">

            <div class="col-md-3">

                <strong>
                    Zaposlenih:
                </strong>

                <?= $employeeCount ?>

            </div>

            <div class="col-md-3">

                <strong>
                    Prisutnih:
                </strong>

                0

            </div>

            <div class="col-md-3">

                <strong>
                    Odsutnih:
                </strong>

                0

            </div>

            <div class="col-md-3">

                <strong>
                    Na GO:
                </strong>

                0

            </div>

        </div>

    </div>

</div>

<div class="row g-3">

    <div class="col-md-3">

        <a
            href="<?= url('organization/my_ou.php') ?>"
            class="dashboard-action text-decoration-none">

            <i class="bi bi-diagram-3"></i>

            <span class="dashboard-action-title">

                Moja OJ

            </span>

        </a>

    </div>

    <div class="col-md-3">

        <a
            href="#"
            class="dashboard-action text-decoration-none">

            <i class="bi bi-check2-square"></i>

                <span class="dashboard-action-title">

                Odobravanje zahteva

            </span>

        </a>

    </div>

    <div class="col-md-3">

        <a
            href="#"
            class="dashboard-action text-decoration-none">

            <i class="bi bi-folder2-open"></i>

                <span class="dashboard-action-title">

                Dokumenta

            </span>

        </a>

    </div>

    <div class="col-md-3">

        <a
            href="#"
            class="dashboard-action text-decoration-none">

            <i class="bi bi-file-earmark-bar-graph"></i>

            <span class="dashboard-action-title">

                Izveštaji

            </span>

        </a>

    </div>

</div>