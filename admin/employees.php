<?php

$pageTitle = "Zaposleni";
include "../layouts/admin_layout_start.php";
require_once '../helpers/audit.php';

require_login();
require_role(['admin', 'hr']);



$units = $conn->query("
    SELECT id, code, name
    FROM organizational_units
    ORDER BY code
");

$employees = $conn->query("
    SELECT *
    FROM employees
    ORDER BY last_name, first_name
");


?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3 mobile-stack">

            <h3>Zaposleni</h3>

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

        <?php include '../partials/search_bar.php'; ?>

        <div class="table-wrapper">

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

                        <th class="text-end" style="width: 180px;">
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
                            <td class="d-flex gap-2">

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
                                    action="<?= BASE_URL ?>admin/actions/employees_delete.php"
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

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php
$units->data_seek(0);
?>

<?php include '../partials/modals/employee_modal.php'; ?>
<?php include 'partials/modals/employee_view_modal.php'; ?>



<?php include "../layouts/footer.php"; ?>