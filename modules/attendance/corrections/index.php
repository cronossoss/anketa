<?php

require_once '../../../config/init.php';
require_once '../../../helpers/organization_tree.php';
require_once '../../../helpers/manager_dashboard.php';

require_login();
require_role(['admin', 'hr', 'manager']);

$pageTitle = 'Korekcije evidencije';

include "../../../layouts/layout_start.php";

$where = '';

if (has_role(['manager'])) {

    $managerEmployeeId =
        (int)$_SESSION['employee_id'];

    $employeeIds =
        getManagedEmployeeIds(
            $conn,
            $managerEmployeeId
        );

    if (empty($employeeIds)) {
        $employeeIds = [0];
    }

    $where =
        "WHERE c.employee_id IN (" .
        implode(
            ',',
            array_map(
                'intval',
                $employeeIds
            )
        ) .
        ")";
}

$sql = "
    SELECT

        c.*,

        e.first_name,
        e.last_name,

        creator_emp.first_name
            AS creator_first_name,

        creator_emp.last_name
            AS creator_last_name

    FROM attendance_corrections c

    INNER JOIN employees e
        ON e.id = c.employee_id

    LEFT JOIN users creator_user
        ON creator_user.id = c.created_by

    LEFT JOIN employees creator_emp
        ON creator_emp.id =
           creator_user.employee_id

    $where

    ORDER BY
        c.correction_date DESC,
        c.correction_time DESC,
        c.created_at DESC
";

$result =
    $conn->query($sql);

$typeLabels = [

    'arrival' =>
    'Dolazak',

    'departure' =>
    'Odlazak',

    'presence' =>
    'Prisustvo',

    'cancel' =>
    'Poništenje'
];
?>

<div class="page-card">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">

            Korekcije evidencije

        </h4>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>

                    <th>Datum</th>

                    <th>Zaposleni</th>

                    <th>Tip</th>

                    <th>Vreme</th>

                    <th>Razlog</th>

                    <th>Izvršio</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                <?php if (
                    $result->num_rows === 0
                ): ?>

                    <tr>

                        <td
                            colspan="7"
                            class="text-center text-muted py-4">

                            Nema evidentiranih korekcija.

                        </td>

                    </tr>

                <?php endif; ?>

                <?php while (
                    $row = $result->fetch_assoc()
                ): ?>

                    <?php

                    $statusClass =
                        match ($row['status']) {

                            'approved'
                            => 'bg-success',

                            'rejected'
                            => 'bg-danger',

                            default
                            => 'bg-warning text-dark'
                        };

                    $type =
                        $typeLabels[$row['correction_type']]
                        ?? $row['correction_type'];

                    ?>

                    <tr>

                        <td>

                            <?= e(
                                date(
                                    'd.m.Y',
                                    strtotime(
                                        $row['correction_date']
                                    )
                                )
                            ) ?>

                        </td>

                        <td>

                            <?= e(
                                $row['first_name']
                                    . ' '
                                    . $row['last_name']
                            ) ?>

                        </td>

                        <td>

                            <?= e($type) ?>

                        </td>

                        <td>

                            <?= e(
                                substr(
                                    $row['correction_time'],
                                    0,
                                    5
                                )
                            ) ?>

                        </td>

                        <td>

                            <?= e(
                                $row['reason']
                            ) ?>

                        </td>

                        <td>

                            <?= e(
                                trim(
                                    ($row['creator_first_name']
                                        ?? '')
                                        . ' '
                                        . ($row['creator_last_name']
                                            ?? '')
                                )
                            ) ?>

                        </td>

                        <td>

                            <span
                                class="badge <?= $statusClass ?>">

                                <?= e(
                                    ucfirst(
                                        $row['status']
                                    )
                                ) ?>

                            </span>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<?php
include "../../../layouts/layout_end.php";
