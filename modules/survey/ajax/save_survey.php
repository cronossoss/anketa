<?php

require_once __DIR__ . "/../../../config/init.php";

require_once __DIR__ . "/../helpers/survey_helper.php";

require_login();



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$survey_id = (int) ($_POST['survey_id'] ?? 0);

$employee_id =
    $_SESSION['user_id'];

$org_unit_id =
    getEmployeeOrgUnit(
        $conn,
        $employee_id
    );

$response_id = createSurveyResponse(
    $conn,
    $survey_id,
    $employee_id,
    $org_unit_id
);

$questions =
    getSurveyQuestions(
        $conn,
        $survey_id
    );

foreach ($questions as $question) {

    if (!$question['required']) {
        continue;
    }

    $field =
        'question_' .
        $question['id'];

    if (
        !isset($_POST[$field]) ||
        empty($_POST[$field])
    ) {

        die("Required question missing.");
    }
}

foreach ($_POST as $key => $value) {

    if (
        strpos($key, 'question_') !== 0
    ) {
        continue;
    }

    $question_id =
        (int) str_replace(
            'question_',
            '',
            $key
        );

    if (is_array($value)) {

        $value =
            implode(', ', $value);
    }

    saveSurveyAnswer(
        $conn,
        $response_id,
        $question_id,
        trim($value)
    );
}

header(
    "Location: ../user/take_survey.php?id=" .
        $survey_id .
        "&success=1"
);

exit;
