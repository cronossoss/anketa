<?php

$pageTitle = "Organizaciona struktura";

include "../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'hr']);

/* =========================
   UNITS
========================= */

$result = $conn->query("
    SELECT
        id,
        name
    FROM organizational_units
");

$units = [];

while ($row = $result->fetch_assoc()) {

    $units[$row['id']] = $row;
}

/* =========================
   RELATIONS
========================= */

$result = $conn->query("
    SELECT
        parent_id,
        child_id
    FROM organizational_relations
    WHERE relation_type = 'hierarchical'
");

$relations = [];

while ($row = $result->fetch_assoc()) {

    $relations[] = $row;
}

/* =========================
   GOJS DATA
========================= */

$parentMap = [];

foreach ($relations as $relation) {

    $parentMap[
        $relation['child_id']
    ] = $relation['parent_id'];
}

$nodes = [];

foreach ($units as $id => $unit) {

    $node = [

        'key' => (int)$id,

        'name' => $unit['name']
    ];

    if (isset($parentMap[$id])) {

        $node['parent'] =
            (int)$parentMap[$id];
    }

    $nodes[] = $node;
}

?>

<style>

#printArea {

    width: 100%;

    height: calc(100vh - 120px);

    border: 1px solid #d1d5db;

    background: #fff;
}

.gojs-btns {

    display: flex;

    gap: 10px;
}

@media print {

    body * {

        visibility: hidden !important;
    }

    #printArea,
    #printArea * {

        visibility: visible !important;
    }

    #printArea {

        position: absolute;

        inset: 0;

        width: 100% !important;

        height: 100% !important;

        border: none !important;
    }

    .sidebar,
    .topbar,
    .gojs-btns {

        display: none !important;
    }

    @page {

        size: A3 landscape;

        margin: 5mm;
    }
}

</style>

<div class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3 class="mb-0">

                    Organizaciona struktura

                </h3>

                <small class="text-muted">

                    PDF prikaz organizacije

                </small>

            </div>

            <div class="gojs-btns">

                <button
                    class="btn btn-secondary"
                    onclick="history.back()">

                    Zatvori

                </button>

                <button
                    class="btn btn-dark"
                    onclick="window.print()">

                    <i class="fa-solid fa-print me-1"></i>

                    Štampaj (PDF)

                </button>

            </div>

        </div>

        <div id="printArea"></div>

    </div>

</div>

<script src="https://unpkg.com/gojs/release/go.js"></script>

<script>

const rawNodes = <?= json_encode(
    $nodes,
    JSON_UNESCAPED_UNICODE
) ?>;

/* FILTER INVALID */

const nodes = rawNodes.map(node => {

    if (
        node.parent === null
        ||
        node.parent === ''
    ) {

        delete node.parent;
    }

    return node;
});

const $ = go.GraphObject.make;

const myDiagram = $(go.Diagram, "printArea", {

    

    initialAutoScale:
        go.Diagram.Uniform,

    layout: $(go.TreeLayout, {

        angle: 90,

        arrangement:
            go.TreeArrangement.FixedRoots,

        layerSpacing: 80,

        nodeSpacing: 35,

        breadthLimit: 1200,

        alignment:
            go.TreeAlignment.CenterChildren,

        compaction:
            go.TreeCompaction.None
    }),

    "undoManager.isEnabled": false
});

myDiagram.background = "#ffffff";



/* NODE */

myDiagram.nodeTemplate = $(

    go.Node,

    "Auto",

    $(

        go.Shape,

        "Rectangle",

        {

            fill: "#ffffff",

            stroke: "#333",

            strokeWidth: 1.5
        }
    ),

    $(

        go.Panel,

        "Vertical",

        {

            margin: 10,

            width: 220
        },

        $(

            go.TextBlock,

            {

                font: "bold 13px Arial",

                stroke: "#111",

                textAlign: "center",

                wrap: go.TextBlock.WrapFit
            },

            new go.Binding(
                "text",
                "name"
            )
        )
    )
);

/* LINKS */

myDiagram.linkTemplate = $(

    go.Link,

    {

        routing: go.Link.Orthogonal,

        corner: 6
    },

    $(

        go.Shape,

        {

            strokeWidth: 2,

            stroke: "#444"
        }
    )
);

/* MODEL */

myDiagram.model = new go.TreeModel(nodes);

</script>

<?php include "../layouts/admin_layout_end.php"; ?>