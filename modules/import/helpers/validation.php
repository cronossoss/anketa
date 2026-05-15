<?php

function validateRequired(
    array $data,
    array $fields
): array {

    $errors = [];

    foreach ($fields as $field) {

        if (
            empty($data[$field])
        ) {

            $errors[] =
                "Nedostaje: {$field}";
        }
    }

    return $errors;
}

?>