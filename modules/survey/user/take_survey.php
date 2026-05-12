<?php

require_once __DIR__ . "/../../../config/init.php";

require_once __DIR__ . "/../helpers/survey_helper.php";

require_login();

$survey_id = $_GET['id'] ?? 0;

$survey = getSurvey($conn, $survey_id);

if (!$survey) {
    die("Survey not found");
}

$questions = getSurveyQuestions($conn, $survey_id);
?>

<?php include "../../../layouts/header.php";
require_once __DIR__ . "/../../../helpers/helpers.php"; ?>

<div class="container py-4">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h2 class="mb-3">
                <?= htmlspecialchars($survey['title']) ?>
            </h2>

            <p class="text-muted">
                <?= nl2br(htmlspecialchars($survey['description'])) ?>
            </p>

            <hr>

            <form method="POST" action="<?= BASE_URL ?>modules/survey/ajax/save_survey.php">

                <input
                    type="hidden"
                    name="survey_id"
                    value="<?= $survey_id ?>">

                <?php
                $current_section = '';
                ?>

                <?php foreach ($questions as $question): ?>

                    <?php
                    $section =
                        $question['section']
                        ?? '';

                    if ($section !== $current_section):

                        $current_section =
                            $section;
                    ?>

                        <div class="mt-5 mb-4">

                            <h4 class="border-bottom pb-2">

                                <?= e($section) ?>

                            </h4>

                        </div>

                    <?php endif; ?>

                    <?php
                    $options =
                        getQuestionOptions(
                            $conn,
                            $question['id']
                        );
                    ?>

                    <div class="mb-4">

                        <label class="form-label fw-semibold">

                            <?= e($question['question']) ?>

                            <?php if ($question['required']): ?>

                                <span class="text-danger">*</span>

                            <?php endif; ?>

                        </label>

                        <?php
                        include "../partials/render_question.php";
                        ?>

                    </div>

                <?php endforeach; ?>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Submit Survey
                </button>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/footer.php"; ?>