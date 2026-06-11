<?php

require_once '../../../config/init.php';
require_once '../../../helpers/organization_tree.php';
require_once '../../../helpers/manager_dashboard.php';

require_login();
require_role(['admin', 'hr', 'manager']);

$id =
    (int)($_GET['id'] ?? 0);

if (!$id) {

    redirect(
        'modules/dashboard/index.php'
    );
}

/*
|--------------------------------------------------------------------------
| UČITAVANJE SLUČAJA
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT

        a.*,

        e.first_name,
        e.last_name,
        e.personal_id,

        t.name AS absence_type,

        CONCAT(
            creator_emp.first_name,
            ' ',
            creator_emp.last_name
        ) AS creator_name,

        CONCAT(
            closer_emp.first_name,
            ' ',
            closer_emp.last_name
) AS closer_name

    FROM attendance_absences a

    INNER JOIN employees e
        ON e.id = a.employee_id

    INNER JOIN attendance_absence_types t
        ON t.id = a.absence_type_id

    LEFT JOIN users creator_user
    ON creator_user.id =
       a.created_by_user_id

    LEFT JOIN employees creator_emp
        ON creator_emp.id =
        creator_user.employee_id

    LEFT JOIN users closer_user
        ON closer_user.id =
        a.closed_by_user_id

    LEFT JOIN employees closer_emp
        ON closer_emp.id =
        closer_user.employee_id

        WHERE a.id = ?
    ";

$stmt =
    $conn->prepare($sql);

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$case =
    $stmt
        ->get_result()
        ->fetch_assoc();

if (!$case) {

    exit(
        'Slučaj nije pronađen.'
    );
}

/*
|--------------------------------------------------------------------------
| OGRANIČENJE ZA MANAGERA
|--------------------------------------------------------------------------
*/

if (has_role(['manager'])) {

    $managedEmployees =
        getManagedEmployeeIds(
            $conn,
            (int)$_SESSION['employee_id']
        );

    if (
        !in_array(
            $case['employee_id'],
            $managedEmployees
        )
    ) {

        exit(
            'Nemate pristup ovom slučaju.'
        );
    }
}

$pageTitle =
    'Pregled slučaja';

include "../../../layouts/layout_start.php";
?>

<div class="page-card">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="mb-0">

            Pregled slučaja

        </h4>

        <a
            href="<?= url(
                'modules/dashboard/index.php'
            ) ?>"
            class="btn btn-secondary">

            Nazad

        </a>

    </div>

    <div class="row">

        <div class="col-md-6">

            <table class="table">

                <tr>

                    <th width="220">

                        Zaposleni

                    </th>

                    <td>

                        <?= e(
                            $case['first_name']
                            . ' '
                            . $case['last_name']
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Personalni broj

                    </th>

                    <td>

                        <?= e(
                            $case['personal_id']
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Status

                    </th>

                    <td>

                        <?= e(
                            $case['absence_type']
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Datum početka

                    </th>

                    <td>

                        <?= e(
                            date(
                                'd.m.Y H:i',
                                strtotime(
                                    $case['date_from']
                                )
                            )
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Datum završetka

                    </th>

                    <td>

                        <?= e(
                            date(
                                'd.m.Y H:i',
                                strtotime(
                                    $case['date_to']
                                )
                            )
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Kreirao

                    </th>

                    <td>

                        <?= e(
                            $case['creator_name']
                            ?? '-'
                        ) ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Kreirano

                    </th>

                    <td>

                        <?= e(
                            date(
                                'd.m.Y H:i',
                                strtotime(
                                    $case['created_at']
                                )
                            )
                        ) ?>

                    </td>

                </tr>

            </table>

        </div>

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">

                    Napomena

                </div>

                <div class="card-body">

                    <?= nl2br(
                        e(
                            $case['note']
                        )
                    ) ?>

                </div>

            </div>

        </div>

    </div>

    <?php if (
        $case['closed_at']
    ): ?>

        <div class="alert alert-success mt-4">

            <strong>

                Slučaj zatvoren

            </strong>

            <hr>

            Zatvorio:

            <?= e(
                $case['closer_name']
                ?? '-'
            ) ?>

            <br>

            Datum:

            <?= e(
                date(
                    'd.m.Y H:i',
                    strtotime(
                        $case['closed_at']
                    )
                )
            ) ?>

            <br><br>

            <?= nl2br(
                e(
                    $case['closure_note']
                )
            ) ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning mt-4">

            Slučaj je još uvek otvoren.

        </div>

        <button
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#closeCaseModal">

            Zatvori slučaj

        </button>

    <?php endif; ?>

</div>

<?php if (!$case['closed_at']): ?>

<div
    class="modal fade"
    id="closeCaseModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="<?= url(
                    'modules/attendance/corrections/close_case.php'
                ) ?>">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="absence_id"
                    value="<?= $case['id'] ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Zatvaranje slučaja

                    </h5>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="closure_note"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Zatvori slučaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php endif; ?>

<?php
include "../../../layouts/layout_end.php";