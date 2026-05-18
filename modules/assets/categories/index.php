<?php

$pageTitle = "Kategorije inventara";

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $safeSearch =
        $conn->real_escape_string($search);

    $where = "
        WHERE
            c.name LIKE '%{$safeSearch}%'
            OR c.code LIKE '%{$safeSearch}%'
            OR t.name LIKE '%{$safeSearch}%'
    ";
}

$result = $conn->query("

    SELECT
        c.*,

        t.name AS type_name

    FROM asset_categories c

    LEFT JOIN asset_types t
        ON t.id = c.asset_type_id

    {$where}

    ORDER BY c.name

");

$types = $conn->query("
    SELECT *
    FROM asset_types
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

            <h4 class="mb-0">

                Kategorije inventara

            </h4>

            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#categoryModal">

                Dodaj kategoriju

            </button>

        </div>

        <!-- SEARCH -->

        <form
            method="GET"
            class="category-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga po tipu, nazivu ili šifri..."
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

                        <th>Tip</th>

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

                                <?= e($row['type_name']) ?>

                            </td>

                            <td>

                                <?= e($row['name']) ?>

                            </td>

                            <td>

                                <?= e($row['code']) ?>

                            </td>

                            <td class="text-nowrap">

                                <div class="d-flex gap-1">

                                    <button
                                        class="btn btn-sm btn-warning edit-category-btn"

                                        data-id="<?= $row['id'] ?>"

                                        data-name="<?= e($row['name']) ?>"

                                        data-code="<?= e($row['code']) ?>"

                                        data-type-id="<?= $row['asset_type_id'] ?>">

                                        <i class="fa-solid fa-pen"></i>

                                    </button>

                                    <form
                                        method="POST"
                                        action="../actions/asset_categories_delete.php"
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
                                            onclick="return confirm('Obrisati kategoriju?')">

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

                <div class="category-mobile-card">

                    <div class="mb-3">

                        <div class="fw-bold">

                            <?= e($row['name']) ?>

                        </div>

                        <div class="small text-muted">

                            <?= e($row['type_name']) ?>

                        </div>

                    </div>

                    <div class="small mb-3">

                        <strong>Šifra:</strong>

                        <?= e($row['code']) ?>

                    </div>

                    <div class="category-mobile-actions">

                        <button
                            class="btn btn-sm btn-warning edit-category-btn"

                            data-id="<?= $row['id'] ?>"

                            data-name="<?= e($row['name']) ?>"

                            data-code="<?= e($row['code']) ?>"

                            data-type-id="<?= $row['asset_type_id'] ?>">

                            <i class="fa-solid fa-pen me-1"></i>

                            Izmeni

                        </button>

                        <form
                            method="POST"
                            action="../actions/asset_categories_delete.php">

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
                                onclick="return confirm('Obrisati kategoriju?')">

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

.category-search {

    display: flex;

    gap: 10px;
}

.category-search .btn {

    min-width: 55px;
}

.category-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.category-mobile-actions {

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

    .category-search {

        flex-direction: column;
    }

    .category-search .btn {

        width: 100%;
    }
}

</style>

<?php include '../partials/category_modal.php'; ?>

<script src="../js/categories.js"></script>

<?php include "../../../layouts/footer.php"; ?>