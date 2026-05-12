<?php

function findUnitById(
    $elements,
    $id
) {

    foreach ($elements as $e) {

        if ((int)$e['id'] === (int)$id) {
            return $e;
        }
    }

    return null;
}

function getRootUnits(
    $units,
    $relations
) {

    $children = [];

    foreach ($relations as $r) {

        $children[] =
            $r['child_id'];
    }

    return array_filter(
        $units,
        function ($u) use ($children) {

            return !in_array(
                $u['id'],
                $children
            );
        }
    );
}

function getVisibleIds(
    $units,
    $filter,
    $parentMap
) {

    if ($filter === 'all') {

        return array_column(
            $units,
            'id'
        );
    }

    $visible = [];

    foreach ($units as $unit) {

        if ($unit['type'] === $filter) {

            $visible[$unit['id']] = true;

            $currentId =
                $unit['id'];

            while (
                isset(
                    $parentMap[$currentId]
                )
            ) {

                $parentId =
                    $parentMap[$currentId];

                $visible[$parentId] = true;

                $currentId =
                    $parentId;
            }
        }
    }

    return array_keys(
        $visible
    );
}
