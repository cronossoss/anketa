<?php

$pageTitle = "Tipovi inventara";
$currentPage = 'asset-types';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT *
    FROM asset_types
    ORDER BY name
");
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3 mobile-stack">



            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#typeModal">

                Dodaj tip

            </button>

        </div>

        <div class="table-wrapper">

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

                            <td>

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

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include '../partials/type_modal.php'; ?>

<script src="../js/types.js"></script>

<?php include "../../../layouts/footer.php"; ?>