<?php

require_once '../../../config/init.php';
require_once '../../../helpers/organization_tree.php';
require_once '../../../helpers/manager_dashboard.php';

require_login();
require_role([
    'admin',
    'hr',
    'manager'
]);

$pageTitle = 'Slučajevi evidencije';

include "../../../layouts/layout_start.php";

$where = '';

if (has_role(['manager'])) {

    $managedEmployees =
        getManagedEmployeeIds(
            $conn,
            (int)$_SESSION['employee_id']
        );

    if (empty($managedEmployees)) {
        $managedEmployees = [0];
    }

    $where =
        "WHERE c.employee_id IN (" .
        implode(
            ',',
            array_map(
                'intval',
                $managedEmployees
            )
        ) .
        ")";
}

$sql = "

    SELECT

        c.*,

        e.first_name,
        e.last_name,
        e.personal_id

    FROM attendance_cases c

    INNER JOIN employees e
        ON e.id = c.employee_id

    $where

    ORDER BY
        c.created_at DESC

";

$result =
    $conn->query($sql);

?>

<div class="page-card">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">

            Slučajevi evidencije

        </h4>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>

                    <th>Zaposleni</th>

                    <th>Tip</th>

                    <th>Status</th>

                    <th>Kreiran</th>

                    <th></th>

                </tr>

            </thead>

            <tbody>

                <?php if ($result->num_rows === 0): ?>

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4">

                            Nema evidentiranih slučajeva.

                        </td>

                    </tr>

                <?php endif; ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php

                    $statusClass =
                        match ($row['case_status']) {

                            'closed'
                                => 'bg-success',

                            'resolved'
                                => 'bg-info',

                            default
                                => 'bg-warning text-dark'
                        };

                    ?>

                    <tr>

                        <td>

                            <strong>

                                <?= e(
                                    $row['first_name']
                                    . ' '
                                    . $row['last_name']
                                ) ?>

                            </strong>

                            <div class="small text-muted">

                                <?= e(
                                    $row['personal_id']
                                ) ?>

                            </div>

                        </td>

                        <td>

                            <?= e(
                                $row['title']
                                ?: $row['case_type']
                            ) ?>

                        </td>

                        <td>

                            <span
                                class="badge <?= $statusClass ?>">

                                <?= e(
                                    ucfirst(
                                        $row['case_status']
                                    )
                                ) ?>

                            </span>

                        </td>

                        <td>

                            <?= date(
                                'd.m.Y H:i',
                                strtotime(
                                    $row['created_at']
                                )
                            ) ?>

                        </td>

                        <td>

                            <a
                                href="<?= url(
                                    'modules/attendance/cases/view.php?id='
                                    . $row['id']
                                ) ?>"
                                class="btn btn-sm btn-primary">

                                Pregled

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<?php
include "../../../layouts/layout_end.php";