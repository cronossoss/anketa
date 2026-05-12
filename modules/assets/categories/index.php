<?php

$pageTitle = "Asset kategorije";

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT *
    FROM asset_categories
    ORDER BY name
");
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3 mobile-stack">

            <h3>Asset kategorije</h3>

            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#categoryModal">

                Dodaj kategoriju

            </button>

        </div>

        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Naziv</th>

                        <th>Šifra</th>

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

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include '../partials/category_modal.php'; ?>

<?php include "../../../layouts/footer.php"; ?>