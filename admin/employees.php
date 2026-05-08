<?php
require_once '../config/db.php';

require_login();
require_admin();

$employees = $conn->query("
    SELECT *
    FROM employees
    ORDER BY last_name, first_name
");

include '../layout/header.php';
?>

<?php include '../layout/sidebar.php'; ?>

<main class="col-lg-10 main-content ms-auto">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3 mobile-stack">

            <h3>Zaposleni</h3>

            <button
                class="btn btn-primary"
                onclick="openModal('employeeModal')">

                Dodaj zaposlenog
            </button>

        </div>

        <?php include '../partials/alerts.php'; ?>

        <?php include '../partials/search_bar.php'; ?>

        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Akcije</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($e = $employees->fetch_assoc()): ?>

                    <tr class="employee-row">

                        <td><?= $e['id'] ?></td>
                        <td><?= $e['first_name'] ?></td>
                        <td><?= $e['last_name'] ?></td>

                        <td>

                            <button
                                class="btn btn-sm btn-primary edit-employee-btn"
                                data-id="<?= $e['id'] ?>">

                                Izmeni
                            </button>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include '../partials/modals/employee_modal.php'; ?>

<script src="assets/js/modules/employees.js"></script>

<?php include '../layout/footer.php'; ?>