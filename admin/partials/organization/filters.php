<?php

/** @var string $filter */
/** @var string $relationType */

// FILTER BUTTONS

$organizationalBtn =
    $relationType === 'organizational'
    ? 'btn-dark'
    : 'btn-outline-dark';

$hierarchicalBtn =
    $relationType === 'hierarchical'
    ? 'btn-dark'
    : 'btn-outline-dark';

$functionalBtn =
    $relationType === 'functional'
    ? 'btn-dark'
    : 'btn-outline-dark';
?>
<?php

// FILTER BUTTONS

$organizationalBtn =
    $relationType === 'organizational'
    ? 'btn-dark'
    : 'btn-outline-dark';

$hierarchicalBtn =
    $relationType === 'hierarchical'
    ? 'btn-dark'
    : 'btn-outline-dark';

$functionalBtn =
    $relationType === 'functional'
    ? 'btn-dark'
    : 'btn-outline-dark';

?>

<div class="organization-filters">

    <div class="mb-3 d-flex gap-2 flex-wrap">

        <a
            href="?relation_type=organizational&filter=<?= $filter ?>"
            class="btn btn-sm <?= $organizationalBtn ?>">

            Organizaciona

        </a>

        <a
            href="?relation_type=hierarchical&filter=<?= $filter ?>"
            class="btn btn-sm <?= $hierarchicalBtn ?>">

            Hijerarhijska

        </a>

        <a
            href="?relation_type=functional&filter=<?= $filter ?>"
            class="btn btn-sm <?= $functionalBtn ?>">

            Funkcionalna

        </a>


        <a
            href="?relation_type=<?= $relationType ?>&filter=all"
            class="btn btn-sm btn-outline-dark">

            Sve

        </a>

        <a
            href="?relation_type=<?= $relationType ?>&filter=OC"
            class="btn btn-sm btn-outline-primary">

            OC

        </a>

        <a
            href="?relation_type=<?= $relationType ?>&filter=OJ"
            class="btn btn-sm btn-outline-success">

            OJ

        </a>

    </div>

</div>