<?php

require_once __DIR__ . "/../../../config/init.php";

require_once __DIR__ . "/../helpers/survey_helper.php";

require_login();
require_admin();

$response_id =
    (int) ($_GET['id'] ?? 0);

$answers =
    getResponseAnswers(
        $conn,
        $response_id
    );
?>

<?php include __DIR__ . "/../../../layouts/header.php"; ?>

<div class="container py-4">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h3 class="mb-4">
                Survey Response
            </h3>

            <?php foreach ($answers as $row): ?>

                <div class="mb-4">

                    <label
                        class="fw-semibold d-block mb-2">
                        <?= e($row['question']) ?>
                    </label>

                    <div class="p-3 bg-light rounded">

                        <?= nl2br(
                            e($row['answer_text'])
                        ) ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>

<?php include __DIR__ . "/../../../layouts/footer.php"; ?>