<?php

$employeeId =
    $_SESSION['employee_id'] ?? 0;

$employee = null;

if ($employeeId) {

    $stmt = $conn->prepare("
        SELECT annual_leave_days
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
}

$annualLeaveDays =
    $employee['annual_leave_days']
    ?? 0;

?>

<div class="row g-3 mb-4">

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div class="dashboard-card-title">

                    <i class="bi bi-calendar-check"></i>

                    <span>Godišnji odmor</span>

                </div>

            </div>

            <div class="dashboard-card-description">

                <?= $annualLeaveDays ?> dana

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div class="dashboard-card-title">

                    <i class="bi bi-door-open"></i>

                    <span>Izlaznice</span>

                </div>

            </div>

            <div class="dashboard-card-description">

                0h / 8h

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div class="dashboard-card-title">

                    <i class="bi bi-clock"></i>

                    <span>Radni sati</span>

                </div>

            </div>

            <div class="dashboard-card-description">

                0 h

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-header">

                <div class="dashboard-card-title">

                    <i class="bi bi-graph-up"></i>

                    <span>Prekovremeno</span>

                </div>

            </div>

            <div class="dashboard-card-description">

                0 h

            </div>

        </div>

    </div>

</div>