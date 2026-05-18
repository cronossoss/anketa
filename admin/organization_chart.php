<?php

$pageTitle = "Organizaciona šema";

include "../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'hr']);

$result = $conn->query("
    SELECT
        id,
        code,
        name
    FROM organizational_units
    ORDER BY code
");

$units = [];

while ($row = $result->fetch_assoc()) {

    $units[$row['id']] = $row;
}

$result = $conn->query("
    SELECT
        parent_id,
        child_id
    FROM organizational_relations
    WHERE relation_type = 'organizational'
");

$relations = [];

while ($row = $result->fetch_assoc()) {

    $relations[] = $row;
}

?>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/treant-js@1.0/Treant.css">

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/0.6.7/css/perfect-scrollbar.min.css">

<style>

#organizationChart {

    width: 100%;

    height: calc(100vh - 140px);

    overflow: auto;

    background: #fff;

    border-radius: 12px;

    padding: 40px;
}

.Treant {

    padding: 40px;
}

.nodeExample1 {

    padding: 8px 12px;

    border: 1px solid #444;

    border-radius: 6px;

    background: #fff;

    min-width: 180px;

    text-align: center;

    font-size: 13px;

    font-weight: 600;
}

.nodeExample1 small {

    display: block;

    margin-top: 4px;

    color: #666;

    font-weight: 400;
}

@media print {

    .sidebar,
    .topbar,
    .btn {

        display: none !important;
    }

    #organizationChart {

        height: auto !important;

        overflow: visible !important;
    }
}

</style>

<div class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3">

            <h3>

                Organizaciona šema

            </h3>

            <button
                class="btn btn-dark"
                onclick="window.print()">

                Štampa / PDF

            </button>

        </div>

        <div id="organizationChart"></div>

    </div>

</div>

<?php

function buildTree(
    $parentId,
    $units,
    $relations
) {

    $children = [];

    foreach ($relations as $relation) {

        if ($relation['parent_id'] == $parentId) {

            $children[] =
                $relation['child_id'];
        }
    }

    $nodes = [];

    foreach ($children as $childId) {

        $child =
            $units[$childId];

        $node = [

            'text' => [

                'name' =>
                    $child['name']
            ]
        ];

        $sub =
            buildTree(
                $childId,
                $units,
                $relations
            );

        if (!empty($sub)) {

            $node['children'] = $sub;
        }

        $nodes[] = $node;
    }

    return $nodes;
}

/* ROOT */

$rootId = null;

foreach ($units as $id => $unit) {

    $hasParent = false;

    foreach ($relations as $relation) {

        if ($relation['child_id'] == $id) {

            $hasParent = true;

            break;
        }
    }

    if (!$hasParent) {

        $rootId = $id;

        break;
    }
}

$tree = [

    'text' => [

        'name' =>
            $units[$rootId]['name']
    ],

    'children' =>
        buildTree(
            $rootId,
            $units,
            $relations
        )
];

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/treant-js@1.0/Treant.min.js"></script>

<script>

var chart_config = {

    chart: {

        container: "#organizationChart",

        rootOrientation: "NORTH",

        connectors: {

            type: "step"
        },

        node: {

            HTMLclass: "nodeExample1"
        }
    },

    nodeStructure:

    <?= json_encode(
            $tree,
            JSON_UNESCAPED_UNICODE
        ) ?>
};

new Treant(chart_config);

</script>

<?php include "../layouts/admin_layout_end.php"; ?>