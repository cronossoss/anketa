<?php

$pageTitle = "Zaposleni";

include "../layouts/layout_start.php";

require_login();

require_role(['admin', 'hr']);

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $safeSearch =
        $conn->real_escape_string($search);

    $where = "
        WHERE
            first_name LIKE '%{$safeSearch}%'
            OR last_name LIKE '%{$safeSearch}%'
            OR personal_id LIKE '%{$safeSearch}%'
            OR position LIKE '%{$safeSearch}%'
    ";
}

$units = $conn->query("
    SELECT id, code, name
    FROM organizational_units
    ORDER BY code
");

$employees = $conn->query("

    SELECT *

    FROM employees

    {$where}

    ORDER BY
        last_name,
        first_name

");

?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex
                    justify-content-between
                    align-items-center
                    flex-wrap
                    gap-2
                    mb-4">

            <button
                type="button"
                id="addEmployeeBtn"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#employeeModal">

                Dodaj zaposlenog

            </button>

        </div>

        <?php include '../partials/alerts.php'; ?>

        <!-- SEARCH -->

        <form
            method="GET"
            class="employee-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga zaposlenih..."
                value="<?= e($search) ?>">

            <button
                type="submit"
                class="btn btn-primary">

                <i class="fa-solid fa-search"></i>

            </button>

        </form>

        <!-- DESKTOP -->

        <div class="table-wrapper d-none d-md-block">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th style="width: 120px;">

                            Matični broj

                        </th>

                        <th>

                            Ime i prezime

                        </th>

                        <th>

                            Pozicija

                        </th>

                        <th
                            class="text-end"
                            style="width: 180px;">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($e = $employees->fetch_assoc()): ?>

                        <tr class="employee-row">

                            <td>

                                <?= e($e['personal_id']) ?>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-link p-0 text-start view-employee-btn"
                                    data-id="<?= $e['id'] ?>">

                                    <?= e($e['first_name']) ?>

                                    <?= e($e['last_name']) ?>

                                </button>

                            </td>

                            <td>

                                <?= e($e['position'] ?? '-') ?>

                            </td>

                            <td class="text-end">

                                <div class="d-inline-flex gap-1">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary edit-employee-btn"

                                        data-id="<?= $e['id'] ?>"

                                        data-bs-toggle="modal"
                                        data-bs-target="#employeeModal"

                                        data-employee='<?= htmlspecialchars(
                                            json_encode($e),
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>'>

                                        Izmeni

                                    </button>

                                    <form
                                        method="POST"
                                        action="<?= url('admin/actions/employees_delete.php') ?>"
                                        onsubmit="return confirm('Obrisati zaposlenog?');">

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= csrf_token() ?>">

                                        <input
                                            type="hidden"
                                            name="delete_id"
                                            value="<?= $e['id'] ?>">

                                        <button
                                            class="btn btn-sm btn-danger">

                                            Obriši

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

        <!-- MOBILE -->

        <?php $employees->data_seek(0); ?>

        <div class="d-block d-md-none">

            <?php while ($e = $employees->fetch_assoc()): ?>

                <div class="employee-mobile-card">

                    <div class="mb-3">

                        <div class="fw-bold">

                            <?= e($e['first_name']) ?>

                            <?= e($e['last_name']) ?>

                        </div>

                        <div class="small text-muted">

                            <?= e($e['personal_id']) ?>

                        </div>

                    </div>

                    <div class="small mb-3">

                        <strong>Pozicija:</strong>

                        <?= e($e['position'] ?? '-') ?>

                    </div>

                    <div class="employee-mobile-actions">

                        <button
                            type="button"
                            class="btn btn-sm btn-secondary view-employee-btn"

                            data-id="<?= $e['id'] ?>">

                            Pregled

                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-primary edit-employee-btn"

                            data-id="<?= $e['id'] ?>"

                            data-bs-toggle="modal"
                            data-bs-target="#employeeModal"

                            data-employee='<?= htmlspecialchars(
                                json_encode($e),
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>'>

                            Izmeni

                        </button>

                        <form
                            method="POST"
                            action="<?= url('admin/actions/employees_delete.php') ?>"
                            onsubmit="return confirm('Obrisati zaposlenog?');">

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= csrf_token() ?>">

                            <input
                                type="hidden"
                                name="delete_id"
                                value="<?= $e['id'] ?>">

                            <button
                                class="btn btn-sm btn-danger w-100">

                                Obriši

                            </button>

                        </form>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</main>

<style>

.employee-search {

    display: flex;

    gap: 10px;
}

.employee-search .btn {

    min-width: 55px;
}

.employee-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.employee-mobile-actions {

    display: flex;

    flex-direction: column;

    gap: 8px;
}

.table td,
.table th {

    padding: 10px 8px;

    vertical-align: middle;

    font-size: 14px;
}

.table th {

    white-space: nowrap;
}

@media (max-width: 768px) {

    .employee-search {

        flex-direction: column;
    }

    .employee-search .btn {

        width: 100%;
    }
}

</style>

<?php

$units->data_seek(0);

?>

<?php include '../partials/modals/employee_modal.php'; ?>

<?php include 'partials/modals/employee_view_modal.php'; ?>

<?php include "../layouts/layout_end.php"; ?>