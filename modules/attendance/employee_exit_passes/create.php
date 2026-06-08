<?php

require_once '../../../config/init.php';

require_login();

$pageTitle =
    'Nova izlaznica';

include "../../../layouts/layout_start.php";

$userId =
    $_SESSION['user_id'];

$userQuery = $conn->prepare("

    SELECT employee_id

    FROM users

    WHERE id = ?

    LIMIT 1

");

$userQuery->bind_param(
    'i',
    $userId
);

$userQuery->execute();

$employeeId =
    $userQuery
        ->get_result()
        ->fetch_assoc()['employee_id'];

$types = $conn->query("

    SELECT

        id,
        name,
        code,

        weekly_limit_minutes,
        monthly_limit_minutes,

        requires_balance

    FROM attendance_exit_types

    WHERE active = 1

    ORDER BY name

");

?>

<div class="container-fluid">
    <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">

        <?= htmlspecialchars($_SESSION['error']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">

        <?= htmlspecialchars($_SESSION['success']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Nova izlaznica

            </h3>

            <div class="text-muted">

                Podnošenje zahteva za izlazak

            </div>

        </div>

        <a
            href="index.php"
            class="btn btn-outline-secondary"
        >

            Nazad

        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="store.php"
            >

                <input
                    type="hidden"
                    name="employee_id"
                    value="<?= $employeeId ?>"
                >

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">

                            Tip izlaska

                        </label>

                        <select
                            name="exit_type_id"
                            id="exit_type_id"
                            class="form-select"
                            required
                        >

                            <option value="">

                                Izaberite

                            </option>

                            <?php while ($type = $types->fetch_assoc()): ?>

                                <option
                                    value="<?= $type['id'] ?>"
                                >

                                    <?= htmlspecialchars(
                                        $type['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Od

                        </label>

                        <input
                            type="datetime-local"
                            name="date_from"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Do

                        </label>

                        <input
                            type="datetime-local"
                            name="date_to"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="col-12">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            rows="3"
                            class="form-control"
                        ></textarea>

                    </div>

                </div>

                <hr>

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-send me-1"></i>

                    Pošalji zahtev

                </button>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>