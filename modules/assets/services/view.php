<?php

$pageTitle = "Pregled servisa";

$currentPage = 'asset-services';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {

    die('Neispravan ID.');
}

/* =========================
   SERVICE
========================= */

$stmt = $conn->prepare("
    SELECT
        s.*,

        a.id AS asset_id,
        a.inventory_number,
        a.manufacturer,
        a.model,
        a.serial_number,

        e.first_name,
        e.last_name

    FROM asset_services s

    LEFT JOIN assets a
        ON a.id = s.asset_id

    LEFT JOIN employees e
        ON e.id = s.technician_id

    WHERE s.id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$service =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$service) {

    die('Servis nije pronađen.');
}

/* =========================
   INTERVENTIONS
========================= */

$interventionsStmt = $conn->prepare("
    SELECT
        i.*,

        e.first_name,
        e.last_name

    FROM asset_service_interventions i

    LEFT JOIN employees e
        ON e.id = i.performed_by

    WHERE i.service_id = ?

    ORDER BY i.performed_at DESC
");

$interventionsStmt->bind_param(
    "i",
    $id
);

$interventionsStmt->execute();

$interventions =
    $interventionsStmt
    ->get_result();
?>

<main class="main-content">

    <!-- HEADER -->

    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

            <div>

                <div class="d-flex align-items-center gap-2 flex-wrap">

                    <h1 class="mb-0">

                        <?= e($service['title']) ?>

                    </h1>

                    <?php

                    $badge = match ($service['status']) {

                        'open' => 'secondary',

                        'diagnostic' => 'warning',

                        'repairing' => 'danger',

                        'waiting_parts' => 'info',

                        'completed' => 'success',

                        'returned' => 'dark',

                        default => 'secondary'
                    };
                    ?>

                    <span class="badge bg-<?= $badge ?>">

                        <?= e(
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $service['status']
                                )
                            )
                        ) ?>

                    </span>

                </div>

                <div class="text-muted mt-1">

                    <?= e($service['inventory_number']) ?>

                    •

                    <?= e(
                        $service['manufacturer']
                            . ' '
                            . $service['model']
                    ) ?>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <!-- LEVO -->

        <div class="col-lg-8">

            <!-- OPIS -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Opis problema

                    </h5>

                </div>

                <div class="card-body">

                    <?= nl2br(
                        e($service['description'])
                    ) ?>

                </div>

            </div>

            <!-- INTERVENCIJE -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        Intervencije

                    </h5>

                    <button
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#interventionModal">

                        Dodaj intervenciju

                    </button>

                </div>

                <div class="card-body">

                    <?php if ($interventions->num_rows > 0): ?>

                        <div class="timeline">

                            <?php while ($item = $interventions->fetch_assoc()): ?>

                                <div class="border-start ps-3 mb-4">

                                    <div class="small text-muted mb-1">

                                        <?= e($item['performed_at']) ?>

                                    </div>

                                    <div class="fw-semibold mb-1">

                                        <?= e(
                                            ucfirst(
                                                $item['intervention_type']
                                            )
                                        ) ?>

                                    </div>

                                    <div class="mb-2">

                                        <?= nl2br(
                                            e($item['description'])
                                        ) ?>

                                    </div>

                                    <?php if ($item['parts_used']): ?>

                                        <div class="small text-muted">

                                            Delovi:
                                            <?= e($item['parts_used']) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endwhile; ?>

                        </div>

                    <?php else: ?>

                        <div class="text-muted">

                            Nema intervencija.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <!-- DESNO -->

        <div class="col-lg-4">

            <!-- INFO -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        Servis info

                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Tip servisa

                        </small>

                        <div>

                            <?= e(
                                ucfirst(
                                    $service['service_type']
                                )
                            ) ?>

                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Serijski broj

                        </small>

                        <div>

                            <?= e(
                                $service['serial_number']
                            ) ?>

                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Prijem

                        </small>

                        <div>

                            <?= e(
                                $service['received_at']
                            ) ?>

                        </div>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Serviser

                        </small>

                        <div>

                            <?=
                            $service['first_name']
                                ? e(
                                    $service['first_name']
                                        . ' '
                                        . $service['last_name']
                                )
                                : '-'
                            ?>

                        </div>

                    </div>

                    <?php if (
                        $service['completed_at']
                    ): ?>

                        <div class="mb-3">

                            <small class="text-muted d-block">

                                Završeno

                            </small>

                            <div>

                                <?= e(
                                    $service['completed_at']
                                ) ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <?php if (
                !in_array(
                    $service['status'],
                    ['completed', 'returned']
                )
            ): ?>

                <hr>

                <form
                    method="POST"
                    action="../actions/service_complete.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= csrf_token() ?>">

                    <input
                        type="hidden"
                        name="service_id"
                        value="<?= $service['id'] ?>">

                    <button
                        type="button"
                        class="btn btn-success w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#completeServiceModal">

                        Završi servis

                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>

</main>

<!-- MODAL -->

<div
    class="modal fade"
    id="interventionModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/service_intervention_create.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="service_id"
                    value="<?= $service['id'] ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Nova intervencija

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Tip intervencije

                        </label>

                        <select
                            name="intervention_type"
                            class="form-select">

                            <option value="diagnostic">
                                Dijagnostika
                            </option>

                            <option value="repair">
                                Popravka
                            </option>

                            <option value="replacement">
                                Zamena dela
                            </option>

                            <option value="software">
                                Softver
                            </option>

                            <option value="maintenance">
                                Održavanje
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Opis

                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Delovi

                        </label>

                        <input
                            type="text"
                            name="parts_used"
                            class="form-control">

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<div
    class="modal fade"
    id="completeServiceModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/service_complete.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="service_id"
                    value="<?= $service['id'] ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Završetak servisa

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Ishod servisa

                        </label>

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="resolution"
                                value="return"
                                checked>

                            <label class="form-check-label">

                                Vrati korisniku

                            </label>

                        </div>

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="resolution"
                                value="unassign">

                            <label class="form-check-label">

                                Razduži uređaj

                            </label>

                        </div>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="resolution"
                                value="dispose">

                            <label class="form-check-label text-danger">

                                Rashoduj uređaj

                            </label>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="4"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Otkaži

                    </button>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Potvrdi

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/footer.php"; ?>