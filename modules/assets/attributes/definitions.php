<?php

$pageTitle = "Definicije atributa";
$currentPage = 'asset-attributes';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT
        a.*,
        c.name AS category_name
    FROM asset_attribute_definitions a
    LEFT JOIN asset_categories c
        ON c.id = a.category_id
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

        <div class="d-flex justify-content-between mb-3 mobile-stack">

            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#attributeModal">

                Dodaj atribut

            </button>

        </div>

        <div class="table-wrapper">

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

                                <?=
                                $row['is_required']
                                    ? 'DA'
                                    : 'NE'
                                ?>

                            </td>

                            <td>

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

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include '../partials/attribute_modal.php'; ?>

<script src="../js/attributes.js"></script>

<?php include "../../../layouts/footer.php"; ?>