<?php

$pageTitle = "Uvoz opreme";

$currentPage = 'asset-import';

include "../../layouts/layout_start.php";

require_login();

require_role(['admin', 'it']);
?>

<main class="main-content">

    <div class="page-card">

        <h3 class="mb-4">

            Uvoz inventara

        </h3>

        <form
            method="POST"
            action="upload.php"
            enctype="multipart/form-data">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>">

            <div class="mb-4">

                <label class="form-label">

                    Excel fajl

                </label>

                <input
                    type="file"
                    name="excel_file"
                    class="form-control"
                    accept=".xlsx,.xls,.csv"
                    required>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Upload i pregled

            </button>

        </form>

    </div>

</main>

<?php include "../../layouts/footer.php"; ?>