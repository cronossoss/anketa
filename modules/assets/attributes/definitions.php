<?php

$pageTitle = "Definicije atributa";

$currentPage = 'asset-attributes';

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
            OR a.name LIKE '%{$safeSearch}%'
            OR a.code LIKE '%{$safeSearch}%'
            OR a.field_type LIKE '%{$safeSearch}%'
    ";
}

$result = $conn->query("

    SELECT
        a.*,

        c.name AS category_name

    FROM asset_attribute_definitions a

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    {$where}

    ORDER BY
        c.name,
        a.sort_order,
        a.name

");

$categories = $conn->query("
    SELECT *
    FROM asset_categories
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

                Definicije atributa

            </h4>

            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#attributeModal">

                Dodaj atribut

            </button>

        </div>

        <!-- SEARCH -->

        <form
            method="GET"
            class="attribute-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga kategorije, naziva, šifre ili tipa..."
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

                        <th>Kategorija</th>

                        <th>Naziv</th>

                        <th>Šifra</th>

                        <th>Tip polja</th>

                        <th>Required</th>

                        <th width="140">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?= e($row['category_name']) ?>

                            </td>

                            <td>

                                <?= e($row['name']) ?>

                            </td>

                            <td>

                                <?= e($row['code']) ?>

                            </td>

                            <td>

                                <?= e($row['field_type']) ?>

                            </td>

                            <td>

                                <?= $row['is_required']
                                    ? 'DA'
                                    : 'NE' ?>

                            </td>

                            <td class="text-nowrap">

                                <div class="d-flex gap-1">

                                    <button
                                        class="btn btn-sm btn-warning edit-attribute-btn"

                                        data-id="<?= $row['id'] ?>"

                                        data-category-id="<?= $row['category_id'] ?>"

                                        data-name="<?= e($row['name']) ?>"

                                        data-code="<?= e($row['code']) ?>"

                                        data-field-type="<?= $row['field_type'] ?>"

                                        data-options="<?= e($row['options']) ?>"

                                        data-required="<?= $row['is_required'] ?>"

                                        data-sort="<?= $row['sort_order'] ?>">

                                        <i class="fa-solid fa-pen"></i>

                                    </button>

                                    <form
                                        method="POST"
                                        action="../actions/asset_attribute_delete.php"
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
                                            onclick="return confirm('Obrisati atribut?')">

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

                <div class="attribute-mobile-card">

                    <div class="mb-3">

                        <div class="fw-bold">

                            <?= e($row['name']) ?>

                        </div>

                        <div class="small text-muted">

                            <?= e($row['category_name']) ?>

                        </div>

                    </div>

                    <div class="small mb-2">

                        <strong>Šifra:</strong>

                        <?= e($row['code']) ?>

                    </div>

                    <div class="small mb-2">

                        <strong>Tip polja:</strong>

                        <?= e($row['field_type']) ?>

                    </div>

                    <div class="small mb-3">

                        <strong>Required:</strong>

                        <?= $row['is_required']
                            ? 'DA'
                            : 'NE' ?>

                    </div>

                    <div class="attribute-mobile-actions">

                        <button
                            class="btn btn-sm btn-warning edit-attribute-btn"

                            data-id="<?= $row['id'] ?>"

                            data-category-id="<?= $row['category_id'] ?>"

                            data-name="<?= e($row['name']) ?>"

                            data-code="<?= e($row['code']) ?>"

                            data-field-type="<?= $row['field_type'] ?>"

                            data-options="<?= e($row['options']) ?>"

                            data-required="<?= $row['is_required'] ?>"

                            data-sort="<?= $row['sort_order'] ?>">

                            <i class="fa-solid fa-pen me-1"></i>

                            Izmeni

                        </button>

                        <form
                            method="POST"
                            action="../actions/asset_attribute_delete.php">

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
                                onclick="return confirm('Obrisati atribut?')">

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

.attribute-search {

    display: flex;

    gap: 10px;
}

.attribute-search .btn {

    min-width: 55px;
}

.attribute-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 12px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.attribute-mobile-actions {

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

    .attribute-search {

        flex-direction: column;
    }

    .attribute-search .btn {

        width: 100%;
    }
}

</style>

<?php include '../partials/attribute_modal.php'; ?>

<script src="../js/attributes.js"></script>

<?php include "../../../layouts/footer.php"; ?>