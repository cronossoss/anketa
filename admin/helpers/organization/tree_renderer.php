<?php

function renderTreeNode(
    $element,
    $parentId,
    $units,
    $employeeStats,
    $isMatched
) {

    $badge = match ($element['type']) {

        'OC' => 'primary',
        'OJ' => 'success',

        default => 'secondary'
    };

    $parentUnit =
        findUnitById(
            $units,
            $parentId
        );

    $showName = true;

    if (
        $parentUnit
        &&
        trim($parentUnit['name'])
        === trim($element['name'])
    ) {

        $showName = false;
    }

    echo '

    <div class="tree-card ' .
        (
            !$isMatched
            ? 'opacity-50'
            : ''
        ) .
        '"
        data-unit-id="' . $element['id'] . '"
        
        data-unit-name="' . e($element['name']) . '">


        <button
            type="button"
            class="tree-edit-btn edit-unit-btn"

            data-id="' . $element['id'] . '"

            data-type="' . e($element['type']) . '"

            data-code="' . e($element['code']) . '"

            data-od_code="' . e($element['od_code']) . '"

            data-name="' . e($element['name']) . '"

            data-description="' . e($element['description']) . '">

            ✏️

        </button>

        <div class="tree-badges">

            <span class="badge bg-' . $badge . '">
                ' . e($element['type']) . '
            </span>

            <span class="tree-code">

                ' . e($element['code']);

    if (!empty($element['od_code'])) {

        echo '
            | OD ' . e($element['od_code']);
    }

    echo '

            </span>

        </div>
    ';

    if ($showName) {

        echo '

            <div class="tree-name">
                ' . e($element['name']) . '
            </div>
        ';
    }

    $stats =
        $employeeStats[$element['id']]
        ?? null;

    if ($stats) {

        echo '

            <div class="tree-stats">

                <div>

                    👥 ' .
            (int)$stats['total'] .
            ' ' .
            pluralize(
                $stats['total'],
                'zaposleni',
                'zaposlena',
                'zaposlenih'
            ) . '

                </div>
        ';

        if ($stats['managers'] > 0) {

            echo '

                <div>

                    👤 ' .
                (int)$stats['managers'] .
                ' ' .
                pluralize(
                    $stats['managers'],
                    'rukovodilac',
                    'rukovodioca',
                    'rukovodilaca'
                ) . '

                </div>
            ';
        }

        echo '

            </div>
        ';
    }

    echo '

    </div>
    ';
}

function renderTree(
    $units,
    $relationMap,
    $relations,
    $employeeStats,
    $visibleIds,
    $parentId = null,
    $level = 0
) {

    global $filter;

    if ($parentId === null) {

        $items = getRootUnits(
            $units,
            $relations
        );
    } else {

        $items =
            $relationMap[$parentId]
            ?? [];
    }

    if (empty($items)) {
        return;
    }

    echo '<ul>';

    foreach ($items as $item) {

        if ($parentId === null) {

            $element = $item;
        } else {

            $element = findUnitById(
                $units,
                $item
            );
        }

        if (!$element) {
            continue;
        }

        $show =
            in_array(
                $element['id'],
                $visibleIds
            );

        $isMatched =
            $filter === 'all'
            ||
            $element['type'] === $filter;

        if ($show) {

            echo '

                <li class="tree-level-' . $level . '">

                    <div class="tree-node-wrapper">

                ';

            renderTreeNode(
                $element,
                $parentId,
                $units,
                $employeeStats,
                $isMatched
            );
        }

        renderTree(
            $units,
            $relationMap,
            $relations,
            $employeeStats,
            $visibleIds,
            $element['id'],
            $level + 1
        );

        echo '

            </div>

        ';

        if ($show) {

            echo '</li>';
        }
    }
    echo '</ul>';
}


