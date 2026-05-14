<?php if (!empty($_SESSION['success'])): ?>

    <div class="alert alert-success">

        <?= $_SESSION['success'] ?>

    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>
<?php

$pageTitle = "IT Inventar";

$currentPage = 'asset-items';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT
        a.*,

        c.name AS category_name,

        e.id AS employee_id,

        e.first_name,
        e.last_name,
        e.personal_id

    FROM assets a

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    LEFT JOIN asset_assignments aa
        ON aa.asset_id = a.id
        AND aa.returned_at IS NULL

    LEFT JOIN employees e
        ON e.id = aa.employee_id

    ORDER BY a.id DESC
");

$categoriesResult = $conn->query("
    SELECT *
    FROM asset_categories
    ORDER BY name
");

$categories = [];

while ($row = $categoriesResult->fetch_assoc()) {

    $categories[] = $row;
}

$employeesResult = $conn->query("
    SELECT
        id,
        first_name,
        last_name,
        personal_id
    FROM employees
    ORDER BY first_name, last_name
");

$employees = [];

while ($row = $employeesResult->fetch_assoc()) {

    $employees[] = $row;
}
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3">

            <h4>

                IT Inventar

            </h4>

            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#assetModal">

                Dodaj inventar

            </button>

        </div>

        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Inventarski broj</th>

                        <th>Kategorija</th>

                        <th>Proizvođač</th>

                        <th>Model</th>

                        <th>Serijski broj</th>

                        <th>Status</th>

                        <th>Zaduženje</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php
                        $isAssigned =
                            !empty($row['employee_id']);
                        ?>

                        <tr class="<?= $isAssigned ? 'table-warning' : '' ?>">

                            <td>

                                <a
                                    href="view.php?id=<?= $row['id'] ?>"
                                    class="fw-semibold text-decoration-none">

                                    <?= e($row['inventory_number']) ?>

                                </a>

                            </td>

                            <td>

                                <?= e($row['category_name']) ?>

                            </td>

                            <td>

                                <?= e($row['manufacturer']) ?>

                            </td>

                            <td>

                                <?= e($row['model']) ?>

                            </td>

                            <td>

                                <?= e($row['serial_number']) ?>

                            </td>

                            <td>

                                <?php

                                $statusClass = match ($row['status']) {

                                    'slobodan' => 'success',

                                    'zaduzen' => 'primary',

                                    'servis' => 'warning',

                                    'rashod' => 'dark',

                                    default => 'secondary'
                                };
                                ?>

                                <span class="badge bg-<?= $statusClass ?>">

                                    <?= e(ucfirst($row['status'])) ?>

                                </span>

                            </td>

                            <td>

                                <?php if ($isAssigned): ?>

                                    <span class="badge bg-success">

                                        <?= e(
                                            $row['first_name']
                                                . ' '
                                                . $row['last_name']
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm assign-btn"

                                        data-id="<?= $row['id'] ?>"

                                        data-name="<?= e(
                                                        $row['inventory_number']
                                                            . ' | '
                                                            . $row['manufacturer']
                                                            . ' '
                                                            . $row['model']
                                                    ) ?>"

                                        data-bs-toggle="modal"
                                        data-bs-target="#assignAssetModal">

                                        <i class="fa-solid fa-user-plus"></i>

                                        Zaduži

                                    </button>
                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<div
    class="modal fade"
    id="assignAssetModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/assign_asset.php">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Zaduženje inventara

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="asset_id"
                        id="assign_asset_id">

                    <div class="mb-3">

                        <label class="form-label">

                            Inventar

                        </label>

                        <input
                            type="text"
                            id="asset_name"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Zaposleni

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                            required>

                            <option value="">

                                Izaberi zaposlenog

                            </option>

                            <?php foreach ($employees as $employee): ?>

                                <option value="<?= $employee['id'] ?>">

                                    <?= e(
                                        $employee['first_name']
                                            . ' '
                                            . $employee['last_name']
                                            . ' ('
                                            . $employee['personal_id']
                                            . ')'
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
    document
        .querySelectorAll('.assign-btn')
        .forEach(btn => {

            btn.addEventListener('click', () => {

                document.getElementById(
                    'assign_asset_id'
                ).value = btn.dataset.id;

                document.getElementById(
                    'asset_name'
                ).value = btn.dataset.name;
            });
        });
</script>
<?php include '../partials/asset_modal.php'; ?>

<script src="../js/assets.js"></script>

<?php include "../../../layouts/footer.php"; ?>