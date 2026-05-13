<?php

$pageTitle = "Dashboard";

include "../layouts/admin_layout_start.php";
require_once '../helpers/audit.php';

require_login();
require_role(['admin', 'hr', 'it', 'manager']);
?>

<div class="row g-4">

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Desktop računari
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Laptop računari
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Potrebni računari
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Obuke
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Ankete
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Izveštaji
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Dokumentacija
                </h6>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <h6 class="text-muted">
                    Planovi
                </h6>

            </div>

        </div>

    </div>

</div>

<?php include "../layouts/footer.php"; ?>