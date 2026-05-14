<?php

$pageTitle = "Pregled inventara";

$currentPage = 'asset-items';

include "../../../layouts/admin_layout_start.php";

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_Login();

if (!hasRole(['admin', 'it'])) {
    die('Nemate dozvolu.');
}

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    die('Neispravan ID.');
}

$stmt = $conn->prepare("
    SELECT
        a.*,

        t.name AS type_name,

        c.name AS category_name

    FROM assets a

    LEFT JOIN asset_types t
        ON t.id = a.asset_type_id

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    WHERE a.id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$asset =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$asset) {
    die('Asset nije pronađen.');
}

/* =========================
   CURRENT ASSIGNMENT
========================= */

$assignmentStmt = $conn->prepare("
    SELECT
        aa.assigned_at,

        e.id AS employee_id,
        e.first_name,
        e.last_name,
        e.personal_id,

        ou.name AS unit_name

    FROM asset_assignments aa

    LEFT JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    WHERE aa.asset_id = ?
    AND aa.returned_at IS NULL

    LIMIT 1
");

$assignmentStmt->bind_param(
    "i",
    $id
);

$assignmentStmt->execute();

$currentAssignment =
    $assignmentStmt
        ->get_result()
        ->fetch_assoc();

/* =========================
   DYNAMIC ATTRIBUTES
========================= */

$attributesStmt = $conn->prepare("
    SELECT
        d.name,
        d.code,
        d.field_type,

        v.value_text

    FROM asset_attribute_values v

    LEFT JOIN asset_attribute_definitions d
        ON d.id = v.attribute_definition_id

    WHERE v.asset_id = ?

    ORDER BY d.sort_order, d.name
");

$attributesStmt->bind_param(
    "i",
    $id
);

$attributesStmt->execute();

$attributes =
    $attributesStmt
        ->get_result();
?>

<main class="main-content">

    <!-- PAGE HEADER -->

    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

            <div>

                <div class="d-flex align-items-center gap-2 flex-wrap">

                    <h1 class="mb-0">

                        <?= e($asset['name']) ?>

                    </h1>

                    <?php
                    $statusClass = match ($asset['status']) {

                        'assigned' => 'success',

                        'repair' => 'danger',

                        'reserve' => 'warning',

                        default => 'secondary'
                    };
                    ?>

                    <span class="badge bg-<?= $statusClass ?>">

                        <?= e(ucfirst($asset['status'])) ?>

                    </span>

                </div>

                <div class="text-muted mt-1">

                    Inventarski broj:
                    <strong>

                        <?= e($asset['inventory_number']) ?>

                    </strong>

                </div>

                <tr>

                    <th width="240">

                        Naziv:

                    </th>

                    <td>

                        <strong><?= e($asset['name']) ?></strong>

                    </td>

                </tr>

            </div>

            <div class="d-flex gap-2">

                <button
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#assetEditModal">

                    <i class="fa-solid fa-pen me-1"></i>

                    Izmeni

                </button>

                <?php if ($currentAssignment): ?>

                    <button class="btn btn-warning btn-sm">

                        <i class="fa-solid fa-user-gear me-1"></i>

                        Promeni zaduženje

                    </button>

                    <form
                        method="POST"
                        action="../actions/unassign_asset.php"
                        onsubmit="return confirm('Razdužiti inventar?');">

                        <input
                            type="hidden"
                            name="asset_id"
                            value="<?= $asset['id'] ?>">

                        <button
                            type="submit"
                            class="btn btn-danger btn-sm">

                            <i class="fa-solid fa-user-minus me-1"></i>

                            Razduži

                        </button>

                    </form>

                        

                <?php else: ?>

                    <button class="btn btn-success btn-sm">

                        <i class="fa-solid fa-user-plus me-1"></i>

                        Zaduži

                    </button>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- GRID -->

    <div class="row g-4">

        <!-- LEFT -->

        <div class="col-lg-8">

            <!-- OSNOVNI PODACI -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Osnovni podaci

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table align-middle mb-0">

                        <tr>

                            <th width="240">

                                Inventarski broj

                            </th>

                            <td>

                                <?= e($asset['inventory_number']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Tip

                            </th>

                            <td>

                                <?= e($asset['type_name']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Kategorija

                            </th>

                            <td>

                                <?= e($asset['category_name']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Status

                            </th>

                            <td>

                                <?= e($asset['status']) ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            <!-- TEHNICKI PODACI -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Tehnički podaci

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table align-middle mb-0">

                        <tr>

                            <th width="240">

                                Proizvođač

                            </th>

                            <td>

                                <?= e($asset['manufacturer'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Model

                            </th>

                            <td>

                                <?= e($asset['model'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Serijski broj

                            </th>

                            <td>

                                <?= e($asset['serial_number'] ?? '-') ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            <!-- DINAMICKI ATRIBUTI -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Dinamički atributi

                    </h5>

                </div>

                <div class="card-body">

                    <?php if ($attributes->num_rows > 0): ?>

                        <div class="row">

                            <?php while ($attr = $attributes->fetch_assoc()): ?>

                                <div class="col-md-4 mb-3">

                                    <small class="text-muted d-block">

                                        <?= e($attr['name']) ?>

                                    </small>

                                    <div class="fw-semibold">

                                        <?= e($attr['value_text']) ?>

                                    </div>

                                </div>

                            <?php endwhile; ?>

                        </div>

                    <?php else: ?>

                        <div class="text-muted">

                            Nema definisanih atributa.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- FINANSIJE -->

            <div class="card shadow-sm border-0">

                <div class="card-header">

                    <h5 class="mb-0">

                        Finansije

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table align-middle mb-0">

                        <tr>

                            <th width="240">

                                Datum kupovine

                            </th>

                            <td>

                                <?= e($asset['purchase_date'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Garancija do

                            </th>

                            <td>

                                <?= e($asset['warranty_until'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Dobavljač

                            </th>

                            <td>

                                <?= e($asset['supplier'] ?? '-') ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Cena

                            </th>

                            <td>

                                <?= e($asset['unit_price'] ?? '-') ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="col-lg-4">

            <!-- ZADUZENJE -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Trenutno zaduženje

                    </h5>

                </div>

                <div class="card-body">

                    <?php if ($currentAssignment): ?>

                        <div class="mb-3">

                            <small class="text-muted d-block">

                                Zaposleni

                            </small>

                            <div class="fw-semibold">

                                <?= e(
                                    $currentAssignment['first_name']
                                        . ' '
                                        . $currentAssignment['last_name']
                                ) ?>

                            </div>

                        </div>

                        <div class="mb-3">

                            <small class="text-muted d-block">

                                Matični broj

                            </small>

                            <div>

                                <?= e($currentAssignment['personal_id']) ?>

                            </div>

                        </div>

                        <div class="mb-3">

                            <small class="text-muted d-block">

                                Organizaciona jedinica

                            </small>

                            <div>

                                <?= e($currentAssignment['unit_name']) ?>

                            </div>

                        </div>

                        <div>

                            <small class="text-muted d-block">

                                Zadužen od

                            </small>

                            <div>

                                <?= e($currentAssignment['assigned_at']) ?>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="text-muted">

                            Inventar trenutno nije zadužen.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</main>

<!-- MODAL za izmenu inventara -->

<div
    class="modal fade"
    id="assetEditModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/assets_update.php">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $asset['id'] ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Izmena inventara

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row g-2">

                        <div class="col-md-6">

                            <label class="form-label">

                                Naziv

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= e($asset['name']) ?>"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Inventarski broj

                            </label>

                            <input
                                type="text"
                                name="inventory_number"
                                class="form-control"
                                value="<?= e($asset['inventory_number']) ?>"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Proizvođač

                            </label>

                            <input
                                type="text"
                                name="manufacturer"
                                class="form-control"
                                value="<?= e($asset['manufacturer']) ?>">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Model

                            </label>

                            <input
                                type="text"
                                name="model"
                                class="form-control"
                                value="<?= e($asset['model']) ?>">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Serijski broj

                            </label>

                            <input
                                type="text"
                                name="serial_number"
                                class="form-control"
                                value="<?= e($asset['serial_number']) ?>">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Status

                            </label>

                            <select
                                name="status"
                                class="form-select">

                                <option
                                    value="active"
                                    <?= $asset['status'] === 'active' ? 'selected' : '' ?>>

                                    Active

                                </option>

                                <option
                                    value="assigned"
                                    <?= $asset['status'] === 'assigned' ? 'selected' : '' ?>>

                                    Assigned

                                </option>

                                <option
                                    value="repair"
                                    <?= $asset['status'] === 'repair' ? 'selected' : '' ?>>

                                    Repair

                                </option>

                                <option
                                    value="reserve"
                                    <?= $asset['status'] === 'reserve' ? 'selected' : '' ?>>

                                    Reserve

                                </option>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj izmene

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/footer.php"; ?>