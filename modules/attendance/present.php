<?php

require_once '../../config/init.php';

$currentPage = 'attendance-present';

$pageTitle = "Trenutno prisutni";

include "../../layouts/admin_layout_start.php";

$date = date('Y-m-d');

$result = $conn->query("

    SELECT

        e.id,
        e.first_name,
        e.last_name,

        ou.code AS organizational_unit,

        ads.first_in,
        ads.last_out,
        ads.late_minutes,
        ads.presence_status,


        (
            SELECT al.direction
            FROM attendance_logs al
            WHERE al.employee_id = e.id
            ORDER BY al.access_datetime DESC
            LIMIT 1
        ) AS last_direction

    FROM employees e

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN attendance_daily_summary ads
        ON ads.employee_id = e.id
        AND ads.work_date = '$date'

    ORDER BY
        ou.name ASC,
        e.last_name ASC

");

?>

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h4 class="mb-1">
                        Trenutno prisutni radnici
                    </h4>

                    <div class="text-muted">

                        LIVE pregled radnika u fabrici

                    </div>

                </div>

                <div>

                    <span class="badge bg-success fs-6">

                        Uživo

                    </span>

                </div>

            </div>

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Zaposleni</th>

                        <th>Organizaciona jedinica</th>

                        <th>Prvi ulaz</th>

                        <th>Izlaz</th>

                        <th>Kašnjenje</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php

                        

                        ?>

                        <tr>

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['first_name']
                                        . ' ' .
                                        $row['last_name']
                                    ) ?>

                                </strong>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['organizational_unit']
                                    ?? '-'
                                ) ?>

                            </td>

                            <td>

                                <?php if ($row['first_in']): ?>

                                    <?= date(
                                        'H:i',
                                        strtotime($row['first_in'])
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($row['last_out']): ?>

                                    <?= date(
                                        'H:i',
                                        strtotime($row['last_out'])
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    $row['late_minutes'] > 0
                                ): ?>

                                    <span class="badge bg-warning text-dark">

                                        <?= $row['late_minutes'] ?>
                                        min

                                    </span>

                                <?php else: ?>

                                    <span class="text-muted">

                                        -

                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php

                                $status =
                                    $row['presence_status'];

                                $lastDirection =
                                    $row['last_direction'];

                                ?>

                                <?php if (
                                    $status === 'present'
                                    &&
                                    $lastDirection === 'IN'
                                ): ?>

                                    <span class="badge bg-success">

                                        Prisutan

                                    </span>

                                <?php elseif (

                                    (
                                        $status === 'present'
                                        ||
                                        $status === 'late'
                                    )

                                    &&

                                    $lastDirection === 'OUT'

                                ): ?>

                                    <span class="badge bg-secondary">

                                        Otišao

                                    </span>

                                <?php elseif (
                                    $status === 'late'
                                    &&
                                    $lastDirection === 'IN'
                                ): ?>

                                    <span class="badge bg-warning text-dark">

                                        Kasni

                                    </span>

                                <?php elseif (
                                    $status === 'doctor'
                                ): ?>

                                    <span class="badge bg-info text-dark">

                                        Lekar

                                    </span>

                                <?php elseif (
                                    $status === 'vacation'
                                ): ?>

                                    <span class="badge bg-primary">

                                        Godišnji

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        Odsutan

                                    </span>

                                <?php endif; ?>

                            </td>
                        

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../../layouts/admin_layout_end.php"; ?>