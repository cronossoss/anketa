<?php

require_once '../../../config/init.php';

require_once
    '../../../helpers/attendance_balances.php';

require_login();

$pageTitle =
    'Zahtevi za odsustvo';

include "../../../layouts/admin_layout_start.php";

$result = $conn->query("

    SELECT

        aa.id,
        aa.employee_id,
        aa.absence_type_id,

        aa.date_from,
        aa.date_to,

        aa.note,

        aa.status,

        e.first_name,
        e.last_name,

        ou.code AS organizational_unit,

        aat.name AS absence_type

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE aa.status = 'pending'

    ORDER BY aa.created_at DESC

");

?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h4 class="mb-1">

                        Zahtevi za odsustvo

                    </h4>

                    <div class="text-muted">

                        Pending approval workflow

                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-sm table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Trajanje</th>

                            <th>Preostalo</th>

                            <th>Napomena</th>

                            <th>Status</th>

                            <th class="text-end">

                                Akcije

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $result->fetch_assoc()): ?>

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

                                    <span class="badge bg-info text-dark">

                                        <?= htmlspecialchars(
                                            $row['absence_type']
                                        ) ?>

                                    </span>

                                </td>

                                <td>

                                    <div>

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_from']
                                            )
                                        ) ?>

                                    </div>

                                    <div class="text-muted small">

                                        do

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_to']
                                            )
                                        ) ?>

                                    </div>

                                </td>

                                <?php

                                $durationMinutes = round(

                                    (
                                        strtotime($row['date_to'])
                                        -
                                        strtotime($row['date_from'])
                                    ) / 60

                                );

                                ?>

                                <td>

                                    <?php if ($durationMinutes >= 480): ?>

                                        <?= round(
                                            $durationMinutes / 480,
                                            1
                                        ) ?> dana

                                    <?php else: ?>

                                        <?= round(
                                            $durationMinutes / 60,
                                            1
                                        ) ?>h

                                    <?php endif; ?>

                                </td>

                                <?php

                                $balance =

                                    get_employee_absence_balance(

                                        $conn,

                                        $row['employee_id'],

                                        $row['absence_type_id'],

                                        $row['date_from']

                                    );

                                ?>

                                <td>

                                    <?php if (
                                        $balance['monthly_remaining']
                                        !== null
                                    ): ?>

                                        <span class="badge bg-light text-dark border">

                                            <?= round(
                                                $balance['monthly_remaining'] / 60,
                                                1
                                            ) ?>h

                                        </span>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $row['note'] ?? ''
                                        )
                                    ) ?>

                                </td>

                                <td>

                                    <?php

                                    $typeClass = 'bg-secondary';

                                    switch ((int)$row['absence_type_id']) {

                                        case 1:
                                            $typeClass = 'bg-info';
                                            break;

                                        case 2:
                                            $typeClass = 'bg-primary';
                                            break;

                                        case 8:
                                            $typeClass = 'bg-success';
                                            break;

                                        case 10:
                                            $typeClass = 'bg-danger';
                                            break;
                                    }

                                    ?>

                                    <span class="badge <?= $typeClass ?>">

                                        <?= htmlspecialchars(
                                            $row['absence_type']
                                        ) ?>

                                    </span>

                                </td>

                                <td class="text-end">

                                    <div class="btn-group btn-group-sm">

                                        <a
                                            href="approve.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success">

                                            <i class="bi bi-check-lg"></i>

                                        </a>

                                        <a
                                            href="reject.php?id=<?= $row['id'] ?>"
                                            class="btn btn-danger">

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

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>