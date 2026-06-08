<?php

$pageTitle = "Zaduženja";

$currentPage = 'asset-assignments';

include "../../../layouts/layout_start.php";

require_login();

if (!canAccessInventory()) {

    http_response_code(403);
    exit('403 Forbidden');
}

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $safeSearch =
        $conn->real_escape_string($search);

    $where = "
        AND (
            e.first_name LIKE '%{$safeSearch}%'
            OR e.last_name LIKE '%{$safeSearch}%'
            OR e.personal_id LIKE '%{$safeSearch}%'
            OR ou.name LIKE '%{$safeSearch}%'
            OR a.inventory_number LIKE '%{$safeSearch}%'
            OR a.manufacturer LIKE '%{$safeSearch}%'
            OR a.model LIKE '%{$safeSearch}%'
        )
    ";
}

$result = $conn->query("

    SELECT

        aa.id,

        aa.asset_id,

        aa.employee_id,

        aa.assigned_at,

        e.personal_id,

        e.first_name,
        e.last_name,

        ou.name AS org_unit_name,

        a.inventory_number,

        a.manufacturer,
        a.model,

        c.name AS category_name

    FROM asset_assignments aa

    LEFT JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN assets a
        ON a.id = aa.asset_id

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    WHERE aa.returned_at IS NULL

    {$where}

    ORDER BY
        e.first_name,
        e.last_name

");

?>

<main class="main-content">

    <div class="page-card">

 
        <!-- SEARCH -->

        <form
            method="GET"
            class="assignment-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga zaposlenog, OJ, inventara..."
                value="<?= e($search) ?>">

            <button
                type="submit"
                class="btn btn-primary">

                <i class="fa-solid fa-search"></i>

            </button>

        </form>

        <?php if (!empty($_SESSION['success'])): ?>

            <div class="alert alert-success">

                <?= $_SESSION['success'] ?>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <!-- DESKTOP -->

        <div class="table-wrapper d-none d-md-block">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Matični broj</th>

                        <th>Zaposleni</th>

                        <th>OJ</th>

                        <th>Inventar</th>

                        <th>Inventarski broj</th>

                        <th width="180">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?= e($row['personal_id']) ?>

                            </td>

                            <td>

                                <?= e(
                                    $row['first_name']
                                    . ' '
                                    . $row['last_name']
                                ) ?>

                            </td>

                            <td>

                                <?= e($row['org_unit_name']) ?>

                            </td>

                            <td>

                                <?= e(
                                    $row['manufacturer']
                                    . ' '
                                    . $row['model']
                                ) ?>

                            </td>

                            <td>

                                <?= e($row['inventory_number']) ?>

                            </td>

                            <td class="text-nowrap">

                                <div class="d-flex gap-1">
                                <button
                                    class="btn btn-sm btn-secondary"

                                    type="button"

                                    data-bs-toggle="collapse"

                                    data-bs-target="#details-<?= $row['id'] ?>">

                                    Detalji

                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning change-assignment-btn"

                                    data-id="<?= $row['id'] ?>"
                                    data-employee-id="<?= $row['employee_id'] ?>"
                                    data-asset-id="<?= $row['asset_id'] ?>"

                                    data-bs-toggle="modal"
                                    data-bs-target="#changeAssignmentModal">

                                    Promena

                                </button>

                                <form
                                    method="POST"
                                    action="../actions/assignment_return.php"
                                    class="d-inline">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= csrf_token() ?>">

                                    <input
                                        type="hidden"
                                        name="assignment_id"
                                        value="<?= $row['id'] ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Razdužiti inventar?')">

                                        Razduži

                                    </button>

                                </form>
                                </div>

                            </td>

                        </tr>

                        <tr>

    <td colspan="6" class="p-0 border-0">

        <div
            class="collapse"
            id="details-<?= $row['id'] ?>">

            <div class="p-3 bg-light">

                <h6>

                    Istorija inventara

                </h6>

                <?php

                $historyStmt =
                    $conn->prepare("
                        SELECT
                            aa.assigned_at,
                            aa.returned_at,

                            e.first_name,
                            e.last_name

                        FROM asset_assignments aa

                        LEFT JOIN employees e
                            ON e.id = aa.employee_id

                        WHERE aa.asset_id = ?

                        ORDER BY aa.assigned_at DESC
                    ");

                $historyStmt->bind_param(
                    "i",
                    $row['asset_id']
                );

                $historyStmt->execute();

                $history =
                    $historyStmt
                    ->get_result();
                ?>

                <ul class="list-group">

                    <?php while ($h = $history->fetch_assoc()): ?>

                        <li class="list-group-item">

                            <strong>

                                <?= e(
                                    $h['first_name']
                                    . ' '
                                    . $h['last_name']
                                ) ?>

                            </strong>

                            |

                            <?= e(
                                $h['assigned_at']
                            ) ?>

                            →

                            <?= e(
                                $h['returned_at']
                                ?? 'Aktivno'
                            ) ?>

                        </li>

                    <?php endwhile; ?>

                </ul>

            </div>

        </div>

    </td>

</tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

        <!-- MOBILE -->

<?php $result->data_seek(0); ?>

<div class="d-block d-md-none">

    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="assignment-mobile-card">

            <div class="d-flex justify-content-between align-items-start mb-2">

                <div>

                    <div class="fw-bold">

                        <?= e(
                            $row['first_name']
                            . ' '
                            . $row['last_name']
                        ) ?>

                    </div>

                    <div class="small text-muted">

                        <?= e($row['personal_id']) ?>

                    </div>

                </div>

                <span class="badge bg-primary">

                    <?= e($row['org_unit_name']) ?>

                </span>

            </div>

            <div class="small mb-2">

                <strong>Inventar:</strong>

                <?= e(
                    $row['manufacturer']
                    . ' '
                    . $row['model']
                ) ?>

            </div>

            <div class="small mb-3">

                <strong>Inventarski broj:</strong>

                <?= e($row['inventory_number']) ?>

            </div>

            <!-- DETALJI -->

            <button
                class="btn btn-sm btn-secondary w-100 mb-2"

                type="button"

                data-bs-toggle="collapse"

                data-bs-target="#details-mobile-<?= $row['id'] ?>">

                Detalji

            </button>

            <!-- ACTIONS -->

            <div class="assignment-mobile-actions mb-3">

                <button
                    type="button"
                    class="btn btn-sm btn-warning change-assignment-btn"

                    data-id="<?= $row['id'] ?>"
                    data-employee-id="<?= $row['employee_id'] ?>"
                    data-asset-id="<?= $row['asset_id'] ?>"

                    data-bs-toggle="modal"
                    data-bs-target="#changeAssignmentModal">

                    Promena

                </button>

                <form
                    method="POST"
                    action="../actions/assignment_return.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= csrf_token() ?>">

                    <input
                        type="hidden"
                        name="assignment_id"
                        value="<?= $row['id'] ?>">

                    <button
                        type="submit"
                        class="btn btn-sm btn-danger w-100"
                        onclick="return confirm('Razdužiti inventar?')">

                        Razduži

                    </button>

                </form>

            </div>

            <!-- COLLAPSE -->

            <div
                class="collapse"
                id="details-mobile-<?= $row['id'] ?>">

                <div class="card card-body small">

                    <?php

                    $historyStmt =
                        $conn->prepare("
                            SELECT
                                aa.assigned_at,
                                aa.returned_at,

                                e.first_name,
                                e.last_name

                            FROM asset_assignments aa

                            LEFT JOIN employees e
                                ON e.id = aa.employee_id

                            WHERE aa.asset_id = ?

                            ORDER BY aa.assigned_at DESC
                        ");

                    $historyStmt->bind_param(
                        "i",
                        $row['asset_id']
                    );

                    $historyStmt->execute();

                    $history =
                        $historyStmt
                        ->get_result();
                    ?>

                    <?php while ($h = $history->fetch_assoc()): ?>

                        <div class="mb-3">

                            <strong>

                                <?= e(
                                    $h['first_name']
                                    . ' '
                                    . $h['last_name']
                                ) ?>

                            </strong>

                            <br>

                            <?= e($h['assigned_at']) ?>

                            →

                            <?= e(
                                $h['returned_at']
                                ?? 'Aktivno'
                            ) ?>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </div>

    <?php endwhile; ?>

</div>

</main>

<!-- MODAL -->

<div
    class="modal fade"
    id="changeAssignmentModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/assignment_change.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="assignment_id"
                    id="change_assignment_id">

                <input
                    type="hidden"
                    name="asset_id"
                    id="change_asset_id">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Promena zaduženja

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Novi zaposleni

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                            required>

                            <option value="">
                                Izaberi zaposlenog
                            </option>

                            <?php

                            $employees = $conn->query("
                                SELECT
                                    id,
                                    first_name,
                                    last_name
                                FROM employees
                                ORDER BY
                                    first_name,
                                    last_name
                            ");

                            while ($emp = $employees->fetch_assoc()):
                            ?>

                                <option value="<?= $emp['id'] ?>">

                                    <?= e(
                                        $emp['first_name']
                                        . ' '
                                        . $emp['last_name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="3"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-warning">

                        Promeni zaduženje

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<style>

.assignment-search {

    display: flex;

    gap: 10px;
}

.assignment-search .btn {

    min-width: 55px;
}

.assignment-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.assignment-mobile-actions {

    display: flex;

    flex-direction: column;

    gap: 8px;
}

.assignment-mobile-card .badge {

    font-size: 11px;
}

.assignment-mobile-actions .btn {

    width: 100%;
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

.table td:last-child {

    width: 170px;
}

@media (max-width: 768px) {

    .assignment-search {

        flex-direction: column;
    }

    .assignment-search .btn {

        width: 100%;
    }
}

</style>

<script>

document
    .querySelectorAll('.change-assignment-btn')
    .forEach(button => {

        button.addEventListener('click', () => {

            document.getElementById(
                'change_assignment_id'
            ).value =
            button.dataset.id;

            document.getElementById(
                'change_asset_id'
            ).value =
            button.dataset.assetId;
        });
    });

</script>

<?php include "../../../layouts/layout_end.php"; ?>