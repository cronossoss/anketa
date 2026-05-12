<?php

if (!isset($question)) {
    return;
}

$type = $question['question_type'];

$name = "question_" . $question['id'];

switch ($type) {

    case 'text':
?>

        <input
            type="text"
            name="<?= $name ?>"
            class="form-control"
            <?= $question['required'] ? 'required' : '' ?>>

    <?php
        break;

    case 'textarea':
    ?>

        <textarea
            name="<?= $name ?>"
            class="form-control"
            rows="4"
            <?= $question['required'] ? 'required' : '' ?>></textarea>

    <?php
        break;

    case 'yes_no':
    ?>

        <div class="form-check">

            <input
                class="form-check-input"
                type="radio"
                name="<?= $name ?>"
                value="Yes"
                <?= $question['required'] ? 'required' : '' ?>>

            <label class="form-check-label">
                Yes
            </label>

        </div>

        <div class="form-check">

            <input
                class="form-check-input"
                type="radio"
                name="<?= $name ?>"
                value="No"
                <?= $question['required'] ? 'required' : '' ?>>

            <label class="form-check-label">
                No
            </label>

        </div>

        <?php
        break;

    case 'radio':

        foreach ($options as $option):
        ?>

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="radio"
                    name="<?= $name ?>"
                    value="<?= htmlspecialchars($option['option_value']) ?>">

                <label class="form-check-label">

                    <?= htmlspecialchars($option['option_text']) ?>

                </label>

            </div>

        <?php
        endforeach;

        break;

    case 'checkbox':

        foreach ($options as $option):
        ?>

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="<?= $name ?>[]"
                    value="<?= htmlspecialchars($option['option_value']) ?>">

                <label class="form-check-label">

                    <?= htmlspecialchars($option['option_text']) ?>

                </label>

            </div>

<?php
        endforeach;

        break;
}
?>