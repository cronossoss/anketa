<?php

$employeeId =
    (int)($_SESSION['employee_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| GODIŠNJI ODMOR
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM attendance_absence_types
    WHERE category = 'vacation'
    LIMIT 1
");

$stmt->execute();

$vacationType =
    $stmt
        ->get_result()
        ->fetch_assoc();

$vacationTypeId =
    (int)($vacationType['id'] ?? 0);

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

$employee =
    $stmt
        ->get_result()
        ->fetch_assoc();

$annualLeaveDays =
    (int)($employee['annual_leave_days'] ?? 0);

$usedLeaveDays = 0;

if ($vacationTypeId) {

    $stmt = $conn->prepare("
        SELECT
            SUM(
                DATEDIFF(
                    DATE(date_to),
                    DATE(date_from)
                ) + 1
            ) AS days_used
        FROM attendance_absences
        WHERE employee_id = ?
          AND absence_type_id = ?
          AND status = 'approved'
    ");

    $stmt->bind_param(
        "ii",
        $employeeId,
        $vacationTypeId
    );

    $stmt->execute();

    $result =
        $stmt
            ->get_result()
            ->fetch_assoc();

    $usedLeaveDays =
        (int)($result['days_used'] ?? 0);
}

$remainingLeaveDays =
    max(
        0,
        $annualLeaveDays - $usedLeaveDays
    );

/*
|--------------------------------------------------------------------------
| IZLAZNICE PRIVATNO
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        monthly_limit_minutes
    FROM attendance_exit_types
    WHERE code = 'PRIV'
    LIMIT 1
");

$stmt->execute();

$exitType =
    $stmt
        ->get_result()
        ->fetch_assoc();

$privateExitTypeId =
    (int)($exitType['id'] ?? 0);

$monthlyLimitMinutes =
    (int)($exitType['monthly_limit_minutes'] ?? 480);

$usedExitMinutes = 0;

if ($privateExitTypeId) {

    $stmt = $conn->prepare("
        SELECT
            SUM(
                TIMESTAMPDIFF(
                    MINUTE,
                    date_from,
                    date_to
                )
            ) AS total_minutes
        FROM attendance_exit_passes
        WHERE employee_id = ?
          AND exit_type_id = ?
          AND status = 'approved'
          AND YEAR(date_from) = YEAR(CURDATE())
          AND MONTH(date_from) = MONTH(CURDATE())
    ");

    $stmt->bind_param(
        "ii",
        $employeeId,
        $privateExitTypeId
    );

    $stmt->execute();

    $result =
        $stmt
            ->get_result()
            ->fetch_assoc();

    $usedExitMinutes =
        (int)($result['total_minutes'] ?? 0);
}

$pendingRequests = 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM attendance_absences
    WHERE employee_id = ?
      AND status = 'pending'
");

$stmt->bind_param(
    "i",
    $employeeId
);

$stmt->execute();

$pendingRequests +=
    (int)$stmt
        ->get_result()
        ->fetch_assoc()['total'];

$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM attendance_exit_passes
    WHERE employee_id = ?
      AND status = 'pending'
");

$stmt->bind_param(
    "i",
    $employeeId
);

$stmt->execute();

$pendingRequests +=
    (int)$stmt
        ->get_result()
        ->fetch_assoc()['total'];

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

                    <?= round($usedExitMinutes / 60, 1) ?>h

                    <small class="text-muted">
                        / <?= round($monthlyLimitMinutes / 60, 1) ?>h
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

                <?= $pendingRequests ?>

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