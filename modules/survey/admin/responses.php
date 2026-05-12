<?php

require_once __DIR__ . "/../../../config/db.php";

require_once __DIR__ . "/../../../helpers/auth.php";

require_once __DIR__ . "/../../../helpers/helpers.php";

require_once __DIR__ . "/../helpers/survey_helper.php";

require_login();
require_admin();

$survey_id =
    (int) ($_GET['id'] ?? 0);

$responses =
    getSurveyResponses(
        $conn,
        $survey_id
    );
?>

<?php include __DIR__ . "/../../../layouts/header.php"; ?>

<div class="container py-4">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h3 class="mb-4">
                Survey Responses
            </h3>

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Org Unit</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>View</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($responses as $row): ?>

                            <tr>

                                <td>
                                    <?= $row['id'] ?>
                                </td>

                                <td>
                                    <?= e(
                                        $row['first_name']
                                            . ' ' .
                                            $row['last_name']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $row['org_unit']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $row['status']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $row['submitted_at']
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        href="/anketa/modules/survey/admin/view_response.php?id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-primary">
                                        View
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . "/../../../layouts/footer.php"; ?>