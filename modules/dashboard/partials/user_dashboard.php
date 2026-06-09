<?php

$employeeId =
    $_SESSION['employee_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| GODIŠNJI ODMOR
|--------------------------------------------------------------------------
*/

$annualLeaveDays = 0;

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

$result =
    $stmt
        ->get_result()
        ->fetch_assoc();

if ($result) {

    $annualLeaveDays =
        (int)$result['annual_leave_days'];
}

/*
|--------------------------------------------------------------------------
| PLACEHOLDER
|--------------------------------------------------------------------------
*/

$usedLeaveDays = 0;

$exitHoursUsed = 0;

$monthlyWorkHours = 0;

$overtimeHours = 0;

?>

<div class="page-card">

    <div class="mb-4">

        <h3 class="mb-1">

            Moj portal

        </h3>

        <div class="text-muted">

            Lični pregled aktivnosti i statusa

        </div>

    </div>

    <!-- KARTICE -->

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-calendar-check"></i>

                    Godišnji odmor

                </div>

                <div class="dashboard-card-value">

                    <?= $annualLeaveDays - $usedLeaveDays ?>

                    <small class="text-muted">

                        / <?= $annualLeaveDays ?>

                    </small>

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

                    <?= $exitHoursUsed ?>

                    <small class="text-muted">

                        / 8h

                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-clock-history"></i>

                    Radni sati

                </div>

                <div class="dashboard-card-value">

                    <?= $monthlyWorkHours ?>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    <i class="bi bi-alarm"></i>

                    Prekovremeno

                </div>

                <div class="dashboard-card-value">

                    <?= $overtimeHours ?>

                </div>

            </div>

        </div>

    </div>

    <!-- MOJI ZAHTEVI -->

    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <strong>

                Moji zahtevi

            </strong>

        </div>

        <div class="card-body">

            <div class="text-muted">

                Pregled zahteva biće prikazan ovde.

            </div>

        </div>

    </div>

    <!-- OBAVEŠTENJA -->

    <div class="card shadow-sm">

        <div class="card-header">

            <strong>

                Obaveštenja

            </strong>

        </div>

        <div class="card-body">

            <?php

            $announcements =
                $conn->query("
                    SELECT
                        title,
                        priority,
                        created_at
                    FROM announcements
                    WHERE active = 1
                    ORDER BY created_at DESC
                    LIMIT 5
                ");

            ?>

            <?php if ($announcements->num_rows): ?>

                <ul class="list-group list-group-flush">

                    <?php while ($a = $announcements->fetch_assoc()): ?>

                        <li class="list-group-item">

                            <strong>

                                <?= e($a['title']) ?>

                            </strong>

                            <div class="small text-muted">

                                <?= e($a['created_at']) ?>

                            </div>

                        </li>

                    <?php endwhile; ?>

                </ul>

            <?php else: ?>

                <div class="text-muted">

                    Nema aktivnih obaveštenja.

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>