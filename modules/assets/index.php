<?php

require_once '../../config/init.php';

$pageTitle = "Inventar";

$currentPage = 'assets-dashboard';

include ROOT_PATH . '/layouts/layout_start.php';

require_login();

if (!canAccessInventory()) {

    http_response_code(403);
    exit('403 Forbidden');
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
        WHERE status = 'zaduzen'
    ")
    ->fetch_assoc()['total'];

$freeAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
        WHERE status = 'slobodno'
    ")
    ->fetch_assoc()['total'];

$repairAssets = $conn
    ->query("
        SELECT COUNT(*) AS total
        FROM assets
        WHERE status = 'servis'
    ")
    ->fetch_assoc()['total'];
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-4">

 
            <a
                href="<?= url('modules/assets/assets/index.php') ?>"
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

<?php include "../../layouts/layout_end.php"; ?>