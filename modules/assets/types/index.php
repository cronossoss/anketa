<?php

$pageTitle = "Tipovi inventara";

$currentPage = 'asset-types';

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
        WHERE
            name LIKE '%{$safeSearch}%'
            OR code LIKE '%{$safeSearch}%'
    ";
}

$result = $conn->query("
    SELECT *
    FROM asset_types

    {$where}

    ORDER BY name
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
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#typeModal">

                Dodaj tip

            </button>

        </div>

        <!-- SEARCH -->

        <form
            method="GET"
            class="type-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga po nazivu ili šifri..."
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

                        <th>Naziv</th>

                        <th>Šifra</th>

                        <th width="120">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?= e($row['name']) ?>

                            </td>

                            <td>

                                <?= e($row['code']) ?>

                            </td>

                            <td class="text-nowrap">

                                <div class="d-flex gap-1">

                                    <button
                                        class="btn btn-sm btn-warning edit-type-btn"

                                        data-id="<?= $row['id'] ?>"

                                        data-name="<?= e($row['name']) ?>"

                                        data-code="<?= e($row['code']) ?>">

                                        <i class="fa-solid fa-edit"></i>

                                    </button>

                                    <form
                                        method="POST"
                                        action="../actions/asset_types_delete.php"
                                        class="d-inline">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= csrf_token() ?>">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $row['id'] ?>">

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Obrisati tip?')">

                                            <i class="fa-solid fa-trash"></i>

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

        <?php $result->data_seek(0); ?>

        <div class="d-block d-md-none">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="type-mobile-card">

                    <div class="mb-3">

                        <div class="fw-bold">

                            <?= e($row['name']) ?>

                        </div>

                        <div class="small text-muted">

                            <?= e($row['code']) ?>

                        </div>

                    </div>

                    <div class="type-mobile-actions">

                        <button
                            class="btn btn-sm btn-warning edit-type-btn"

                            data-id="<?= $row['id'] ?>"

                            data-name="<?= e($row['name']) ?>"

                            data-code="<?= e($row['code']) ?>">

                            <i class="fa-solid fa-edit me-1"></i>

                            Izmeni

                        </button>

                        <form
                            method="POST"
                            action="../actions/asset_types_delete.php">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= csrf_token() ?>">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $row['id'] ?>">

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger w-100"
                                onclick="return confirm('Obrisati tip?')">

                                <i class="fa-solid fa-trash me-1"></i>

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

.type-search {

    display: flex;

    gap: 10px;
}

.type-search .btn {

    min-width: 55px;
}

.type-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.type-mobile-actions {

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

    .type-search {

        flex-direction: column;
    }

    .type-search .btn {

        width: 100%;
    }
}

</style>

<?php include '../partials/type_modal.php'; ?>

<script src="../js/types.js"></script>

<?php include "../../../layouts/layout_end.php"; ?>