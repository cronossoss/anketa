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

                </div>

                <?php include "partials/organization/filters.php"; ?>

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

<?php include __DIR__ . "/partials/modals/organization_employees_modal.php"; ?>

<?php include __DIR__ . "/partials/modals/organization_modal.php"; ?>

<?php include __DIR__ . "/partials/modals/employee_view_modal.php"; ?>

<script src="<?= BASE_URL ?>assets/js/modules/organization.js?v=2"></script>

<?php include "../layouts/admin_layout_end.php"; ?>