<?php

$pageTitle = "Inventar";

$currentPage = 'assets-dashboard';

include "../../layouts/admin_layout_start.php";

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/modules/assets/helpers/permissions.php';

require_Login();

if (!hasRole(['admin', 'it'])) {

    die('Nemate dozvolu.');
}

/* =========================
   COUNTERS
========================= */

$totalAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
    ")
    ->fetch_assoc()['total'];

$assignedAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
        WHERE status = 'assigned'
    ")
    ->fetch_assoc()['total'];

$freeAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
        WHERE status != 'assigned'
    ")
    ->fetch_assoc()['total'];

$repairAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
        WHERE status = 'repair'
    ")
    ->fetch_assoc()['total'];
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="mb-0">

                Inventar

            </h3>

            <a
                href="<?= BASE_URL ?>modules/assets/assets/index.php"
                class="btn btn-primary">

                IT Inventar

            </a>

        </div>

        <div class="row g-3">

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted small mb-1">

                            Ukupno uređaja

                        </div>

                        <h2 class="mb-0">

                            <?= $totalAssets ?>

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted small mb-1">

                            Zaduženi

                        </div>

                        <h2 class="mb-0 text-success">

                            <?= $assignedAssets ?>

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted small mb-1">

                            Slobodni

                        </div>

                        <h2 class="mb-0 text-warning">

                            <?= $freeAssets ?>

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted small mb-1">

                            Na servisu

                        </div>

                        <h2 class="mb-0 text-danger">

                            <?= $repairAssets ?>

                        </h2>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "../../layouts/footer.php"; ?>