<?php

/** @var array $units */
/** @var array $relationMap */
/** @var array $relations */
/** @var array $employeeStats */
/** @var array $visibleIds */

?>

<!-- TREE -->

<div class="org-tree">

    <?php renderTree(
        $units,
        $relationMap,
        $relations,
        $employeeStats,
        $visibleIds
    ); ?>

</div>