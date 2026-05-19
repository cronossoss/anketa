<?php

$pageTitle = "Organizacija";

include "../layouts/admin_layout_start.php";
require_once '../helpers/audit.php';

require_login();
require_role(['admin', 'hr']);

require_once "helpers/organization/tree_helpers.php";
require_once "helpers/organization/tree_renderer.php";

/* =========================
   DATA
========================= */

$result = $conn->query("
    SELECT *
    FROM organizational_units
    ORDER BY code
");

$units =
    $result->fetch_all(MYSQLI_ASSOC);

/* =========================
   FILTER
========================= */

$filter =
    $_GET['filter']
    ?? 'all';

$relationType =
    $_GET['relation_type']
    ?? 'organizational';

/* =========================
   EMPLOYEE STATS
========================= */

$employeeStats = [];

$result = $conn->query("
    SELECT
        organizational_unit_id,
        COUNT(*) AS total,
        SUM(is_manager = 1) AS managers
    FROM employees
    GROUP BY organizational_unit_id
");

while ($row = $result->fetch_assoc()) {

    $employeeStats[$row['organizational_unit_id']] = $row;
}

/* =========================
   RELATIONS
========================= */

$stmt = $conn->prepare("
    SELECT *
    FROM organizational_relations
    WHERE relation_type = ?
    ORDER BY sort_order, id
");

$stmt->bind_param(
    "s",
    $relationType
);

$stmt->execute();

$result =
    $stmt->get_result();

$relations =
    $result->fetch_all(MYSQLI_ASSOC);


/* =========================
   RELATION MAP
========================= */

$relationMap = [];

foreach ($relations as $relation) {

    $relationMap[$relation['parent_id']][] = $relation['child_id'];
}

$parentMap = [];

foreach ($relations as $relation) {

    $parentMap[$relation['child_id']] = $relation['parent_id'];
}

/* =========================
   VISIBLE IDS
========================= */

$visibleIds = getVisibleIds(
    $units,
    $filter,
    $parentMap
);

/* =========================
   HELPERS
========================= */

?>

<main class="main-content">

    <div class="row">

        <?php include "partials/organization/sidebar.php"; ?>

        <!-- LEFT -->

        <div class="col-lg-8 mb-4">

            <div class="page-card">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <h4 class="mb-0">

                        Pregled organizacije

                    </h4>

                    <a
                        href="organization_print.php"
                        class="btn btn-dark">

                        <i class="fa-solid fa-print me-2"></i>

                        PDF / Štampa

                    </a>

                </div>

                <?php include "partials/organization/filters.php"; ?>

                <div class="print-title">

                    Organizaciona struktura

                </div>

                <?php include "partials/organization/tree.php"; ?>

            </div>

        </div>



    </div>

</main>



<?php

$units = $conn->query("
    SELECT
        id,
        code,
        name
    FROM organizational_units
    ORDER BY name
");
?>

    <style>

.org-tree ul {

    list-style: none;

    margin: 0;

    padding-left: 28px;

    position: relative;
}

.org-tree li {

    position: relative;

    margin-bottom: 14px;
}

/* VERTICAL LINE */

.org-tree li::before {

    content: '';

    position: absolute;

    left: -18px;

    top: -14px;

    width: 2px;

    height: calc(100% + 20px);

    background: #d1d5db;
}

/* HORIZONTAL CONNECTOR */

.org-tree li::after {

    content: '';

    position: absolute;

    left: -18px;

    top: 34px;

    width: 16px;

    height: 2px;

    background: #d1d5db;
}

/* ROOT */

.org-tree > ul > li::before,
.org-tree > ul > li::after {

    display: none;
}

/* LAST CHILD */

.org-tree li:last-child::before {

    height: 48px;
}

/* CARD */

.tree-card {

    position: relative;

    background: #fff;

    border: 1px solid #dbe2ea;

    border-radius: 14px;

    padding: 14px 16px;

    box-shadow: 0 2px 8px rgba(0,0,0,0.04);

    transition: all 0.2s ease;
}

.tree-card:hover {

    border-color: #2563eb;

    transform: translateX(2px);
}

/* NAME */

.tree-name {

    font-size: 15px;

    font-weight: 700;

    margin-top: 8px;

    line-height: 1.4;
}

/* BADGES */

.tree-badges {

    display: flex;

    align-items: center;

    gap: 8px;

    flex-wrap: wrap;
}

/* CODE */

.tree-code {

    font-size: 12px;

    color: #6b7280;
}

/* STATS */

.tree-stats {

    margin-top: 10px;

    font-size: 13px;

    color: #6b7280;

    display: flex;

    gap: 12px;

    flex-wrap: wrap;
}

/* EDIT BUTTON */

.tree-edit-btn {

    position: absolute;

    top: 10px;

    right: 10px;

    border: none;

    background: transparent;

    opacity: 0.5;

    transition: 0.2s;
}

.tree-edit-btn:hover {

    opacity: 1;

    transform: scale(1.1);
}

/* MOBILE */

@media (max-width: 768px) {

    .org-tree ul {

        padding-left: 18px;
    }

    .tree-card {

        padding: 12px;
    }

    .tree-stats {

        flex-direction: column;

        gap: 4px;
    }
}

</style>


<?php include __DIR__ . "/partials/modals/organization_employees_modal.php"; ?>

<?php include __DIR__ . "/partials/modals/organization_modal.php"; ?>

<?php include __DIR__ . "/partials/modals/employee_view_modal.php"; ?>

<script
    src="<?= url('assets/js/modules/employee-view.js') ?>?v=<?= time() ?>">
</script>


<script
    src="<?= url('assets/js/modules/organization.js') ?>?v=<?= time() ?>">
</script>




<?php include "../layouts/admin_layout_end.php"; ?>