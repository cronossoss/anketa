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

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    redirect(
        'modules/attendance/cases/index.php'
    );
}

$stmt = $conn->prepare("

    SELECT

        c.*,

        e.first_name,
        e.last_name,
        e.personal_id,

        creator_employee.first_name
            AS creator_first_name,

        creator_employee.last_name
            AS creator_last_name

    FROM attendance_cases c

    INNER JOIN employees e
        ON e.id = c.employee_id

    LEFT JOIN users creator_user
        ON creator_user.id =
           c.created_by_user_id

    LEFT JOIN employees creator_employee
        ON creator_employee.id =
           creator_user.employee_id

    WHERE c.id = ?

    LIMIT 1

");

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

    $_SESSION['error'] =
        'Slučaj nije pronađen.';

    redirect(
        'modules/attendance/cases/index.php'
    );
}

$pageTitle =
    'Pregled slučaja';

include "../../../layouts/layout_start.php";
?>

<div class="page-card">

    <div class="d-flex justify-content-between mb-4">

        <h3>

            Pregled slučaja

        </h3>

        <a
            href="<?= url(
                'modules/attendance/cases/index.php'
            ) ?>"
            class="btn btn-secondary">

            Nazad

        </a>

    </div>

    <table class="table">

        <tr>

            <th width="250">

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

                Tip slučaja

            </th>

            <td>

                <?= e(
                    $case['title']
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Opis

            </th>

            <td>

                <?= nl2br(
                    e(
                        $case['description']
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
                    trim(
                        ($case['creator_first_name'] ?? '')
                        . ' '
                        . ($case['creator_last_name'] ?? '')
                    )
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Kreirano

            </th>

            <td>

                <?= date(
                    'd.m.Y H:i',
                    strtotime(
                        $case['created_at']
                    )
                ) ?>

            </td>

        </tr>

    </table>

    <hr>

    <h5 class="mb-3">

        Razrešavanje slučaja

    </h5>

    <form
        method="POST"
        action="<?= url(
            'modules/attendance/cases/resolve_case.php'
        ) ?>">

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf_token() ?>">

        <input
            type="hidden"
            name="case_id"
            value="<?= $case['id'] ?>">

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="form-label">

                    Konačna odluka

                </label>

                <select
                    name="resolution_type"
                    class="form-select"
                    required>

                    <option value="present">
                        Prisutan
                    </option>

                    <option value="doctor">
                        Odlazak kod lekara
                    </option>

                    <option value="private_exit">
                        Privatni izlazak
                    </option>

                    <option value="official_exit">
                        Službeni izlazak
                    </option>

                    <option value="business_trip">
                        Službeni put
                    </option>

                    <option value="sick_leave">
                        Bolovanje
                    </option>

                    <option value="vacation">
                        Godišnji odmor
                    </option>

                    <option value="unpaid_leave">
                        Neplaćeno odsustvo
                    </option>

                    <option value="unexcused">
                        Neopravdani izostanak
                    </option>

                    <option value="tardiness">
                        Kašnjenje
                    </option>

                </select>

            </div>

        </div>

        <div class="row">

            <div class="col-md-3 mb-3">

                <label class="form-label">

                    Datum od

                </label>

                <input
                    type="date"
                    name="date_from"
                    class="form-control"
                    value="<?= date('Y-m-d') ?>">

            </div>

            <div class="col-md-3 mb-3">

                <label class="form-label">

                    Vreme od

                </label>

                <input
                    type="time"
                    name="time_from"
                    class="form-control">

            </div>

            <div class="col-md-3 mb-3">

                <label class="form-label">

                    Datum do

                </label>

                <input
                    type="date"
                    name="date_to"
                    class="form-control"
                    value="<?= date('Y-m-d') ?>">

            </div>

            <div class="col-md-3 mb-3">

                <label class="form-label">

                    Vreme do

                </label>

                <input
                    type="time"
                    name="time_to"
                    class="form-control">

            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">

                Napomena rukovodioca

            </label>

            <textarea
                name="resolution_note"
                class="form-control"
                rows="4"></textarea>

        </div>

        <button
            type="submit"
            class="btn btn-success">

            Sačuvaj i zatvori slučaj

        </button>

    </form>

</div>

<?php
include "../../../layouts/layout_end.php";