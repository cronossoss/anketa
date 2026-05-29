<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Izlaznice';

include "../../../layouts/admin_layout_start.php";

                $pendingRequests = $conn->query("

                    SELECT

                        ep.id,

                        ep.date_from,
                        ep.date_to,

                        ep.status,

                        et.name AS exit_type,

                        e.first_name,
                        e.last_name,

                        ou.code AS organizational_unit

                    FROM attendance_exit_passes ep

                    JOIN employees e
                        ON e.id = ep.employee_id

                    LEFT JOIN organizational_units ou
                        ON ou.id = e.organizational_unit_id

                    JOIN attendance_exit_types et
                        ON et.id = ep.exit_type_id

                    WHERE ep.status = 'pending'

                    AND ep.source = 'employee_request'

                    ORDER BY ep.created_at ASC

                ");

            
?>

<div class="container-fluid">

    <div class="mb-4">

        <div class="card-body">

            <?php

            $result = $conn->query("

                SELECT

                    ep.id,

                    ep.employee_id,

                    ep.date_from,
                    ep.date_to,

                    ep.status,
                    ep.source,

                    et.name AS exit_type,

                    e.first_name,
                    e.last_name,

                    ou.code AS organizational_unit

                FROM attendance_exit_passes ep

                JOIN employees e
                    ON e.id = ep.employee_id

                LEFT JOIN organizational_units ou
                    ON ou.id = e.organizational_unit_id

                JOIN attendance_exit_types et
                    ON et.id = ep.exit_type_id

                WHERE ep.status IN ('approved','rejected')

                ORDER BY ep.date_from DESC

            ");

            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="mb-1">

                        Upravljanje izlaznicama

                    </h3>

                    <div class="text-muted">

                        Zahtevi zaposlenih i evidencija izlaznica

                    </div>

                </div>

                <a
                    href="create.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-plus-lg me-1"></i>

                    Nova izlaznica

                </a>

            </div>

          

            

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-warning-subtle">

                    <strong>

                        Zahtevi zaposlenih za odobravanje

                    </strong>

                </div>

                <div class="card-body p-0">

                <div class="table-responsive">

<table class="table table-sm table-hover mb-0">

    <thead>

        <tr>

            <th>Zaposleni</th>

            <th>Tip</th>

            <th>Period</th>

            <th>Trajanje</th>

            <th class="text-end">

                Akcije

            </th>

        </tr>

    </thead>

    <tbody>

    <?php while ($row = $pendingRequests->fetch_assoc()): ?>

        <?php if ($pendingRequests->num_rows == 0): ?>

<tr>

    <td colspan="5" class="text-center text-muted py-4">

        Nema zahteva za odobravanje.

    </td>

</tr>

<?php endif; ?>

        <?php

        $durationMinutes = round(

            (
                strtotime($row['date_to'])
                -
                strtotime($row['date_from'])
            ) / 60

        );

        ?>

        <tr>

            <td>

                <strong>

                    (<?= htmlspecialchars(
                        $row['organizational_unit']
                    ) ?>)

                    <?= htmlspecialchars(
                        $row['last_name']
                        . ' '
                        . $row['first_name']
                    ) ?>

                </strong>

            </td>

            <td>

                <?= htmlspecialchars(
                    $row['exit_type']
                ) ?>

            </td>

            <td>

                <?= date(
                    'd.m.Y H:i',
                    strtotime($row['date_from'])
                ) ?>

                -

                <?= date(
                    'd.m.Y H:i',
                    strtotime($row['date_to'])
                ) ?>

            </td>

            <td>

                <?= round(
                    $durationMinutes / 60,
                    1
                ) ?>h

            </td>

            <td class="text-end">

                <div class="btn-group btn-group-sm">

                    <a
                        href="approve.php?id=<?= $row['id'] ?>"
                        class="btn btn-success"
                    >

                        <i class="bi bi-check-lg"></i>

                    </a>

                    <a
                        href="reject.php?id=<?= $row['id'] ?>"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-x-lg"></i>

                    </a>

                </div>

            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>

</div>

    </div>
</div>

<div class="card border-0 shadow-sm">

    <div class="card-header">

        <strong>

            Istorija izlaznica

        </strong>

    </div>

    <div class="card-body">

            <div class="table-responsive">

                <table class="table table-sm table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Trajanje</th>

                            <th>Status</th>

                            <th>Izvor</th>

                           
                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($result->num_rows == 0): ?>

<tr>

    <td colspan="6" class="text-center text-muted py-4">

        Nema evidentiranih izlaznica.

    </td>

</tr>

<?php endif; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <?php

                            $durationMinutes = round(

                                (
                                    strtotime($row['date_to'])
                                    -
                                    strtotime($row['date_from'])
                                ) / 60

                            );

                            ?>

                            <tr>

                                <td>

                                    <strong>

                                        (<?= htmlspecialchars(
                                            $row['organizational_unit']
                                        ) ?>)

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                            . ' ' .
                                            $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['exit_type']
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            $row['date_from']
                                        )
                                    ) ?>

                                    <br>

                                    <small class="text-muted">

                                        do

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_to']
                                            )
                                        ) ?>

                                    </small>

                                </td>

                                <td>

                                    <?= round(
                                        $durationMinutes / 60,
                                        1
                                    ) ?>h

                                </td>

                                <td>

                                    <?php if ($row['status'] === 'approved'): ?>

                                        <span class="badge bg-success">

                                            Odobreno

                                        </span>

                                    <?php elseif ($row['status'] === 'rejected'): ?>

                                        <span class="badge bg-danger">

                                            Odbijeno

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-warning text-dark">

                                            Na čekanju

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php

                                    $sourceLabel = [

                                        'manager_created' => 'Rukovodilac',
                                        'employee_request' => 'Zahtev zaposlenog',
                                        'hr_created' => 'HR'

                                    ];

                                    echo htmlspecialchars(
                                        $sourceLabel[$row['source']]
                                        ?? $row['source']
                                    );

                                    ?>

                                </td>

                                
                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>