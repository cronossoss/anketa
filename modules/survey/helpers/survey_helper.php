<?php

function getSurvey($conn, $survey_id)
{
    $sql = "
        SELECT *
        FROM surveys
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $survey_id);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

function getSurveyQuestions($conn, $survey_id)
{
    $sql = "
        SELECT *
        FROM survey_questions
        WHERE survey_id = ?
        ORDER BY sort_order ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $survey_id);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getQuestionOptions($conn, $question_id)
{
    $sql = "
        SELECT *
        FROM survey_options
        WHERE question_id = ?
        ORDER BY sort_order ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $question_id);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function createSurveyResponse(
    $conn,
    $survey_id,
    $employee_id,
    $org_unit_id
) {

    $sql = "
        INSERT INTO survey_responses
        (
            survey_id,
            employee_id,
            org_unit_id,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            'submitted'
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iii",
        $survey_id,
        $employee_id,
        $org_unit_id
    );

    $stmt->execute();

    return $conn->insert_id;
}

function saveSurveyAnswer(
    $conn,
    $response_id,
    $question_id,
    $answer
) {

    $sql = "
        INSERT INTO survey_answers
        (
            response_id,
            question_id,
            answer_text
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iis",
        $response_id,
        $question_id,
        $answer
    );

    return $stmt->execute();
}


function getEmployeeOrgUnit(
    $conn,
    $employee_id
) {

    $sql = "
        SELECT organizational_unit_id
        FROM employees
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $employee_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $employee =
        $result->fetch_assoc();

    return
        $employee['organizational_unit_id']
        ?? null;
}


function getSurveyResponses(
    $conn,
    $survey_id
) {

    $sql = "
        SELECT

            sr.id,
            sr.submitted_at,
            sr.status,

            e.first_name,
            e.last_name,

            ou.name AS org_unit

        FROM survey_responses sr

        LEFT JOIN employees e
            ON sr.employee_id = e.id

        LEFT JOIN organizational_units ou
            ON sr.org_unit_id = ou.id

        WHERE sr.survey_id = ?

        ORDER BY sr.id DESC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $survey_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getResponseAnswers(
    $conn,
    $response_id
) {

    $sql = "
        SELECT

            sa.answer_text,

            sq.question,
            sq.question_type,
            sq.section

        FROM survey_answers sa

        INNER JOIN survey_questions sq
            ON sa.question_id = sq.id

        WHERE sa.response_id = ?

        ORDER BY sq.sort_order ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $response_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}
