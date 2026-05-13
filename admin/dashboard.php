<?php

$pageTitle = "Dashboard";

include "../layouts/admin_layout_start.php";
require_once '../helpers/audit.php';

require_login();
require_role(['admin', 'hr', 'it', 'manager']);
?>

<div class="row g-4">

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                <i class="fas fa-laptop"></i>
            </div>

            <div class="dashboard-card-content">

                <h3>Assets</h3>

                <p>
                    Upravljanje IT opremom i inventarom
                </p>

                <a
                    href="/anketa/modules/assets/items/index.php"
                    class="btn btn-primary"
                >
                    Otvori modul
                </a>

            </div>

        </div>

        

    </div>

    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>


    

    

    

    
</div>

<?php include "../layouts/footer.php"; ?>