<?php

$pageTitle = "Prijem u servis";

$currentPage = 'asset-services';

include "../../../layouts/admin_layout_start.php";

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_login();

require_role(['admin', 'it']);

$assets = $conn->query("
    SELECT
        id,
        inventory_number,
        manufacturer,
        model,
        status
    FROM assets
    WHERE status NOT IN ('repair')
    ORDER BY inventory_number
");
?>

<main class="main-content">

    <div class="page-card">

        <h4 class="mb-4">

            Prijem inventara u servis

        </h4>

        <form
            method="POST"
            action="../actions/service_create.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>">

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">

                        Inventar

                    </label>

                    <select
                        name="asset_id"
                        class="form-select"
                        required>

                        <option value="">
                            Izaberi inventar
                        </option>

                        <?php while ($asset = $assets->fetch_assoc()): ?>

                            <option value="<?= $asset['id'] ?>">

                                <?= e(
                                    $asset['inventory_number']
                                        . ' | '
                                        . $asset['manufacturer']
                                        . ' '
                                        . $asset['model']
                                        . ' ['
                                        . strtoupper($asset['status'])
                                        . ']'
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="form-label">

                        Tip servisa

                    </label>

                    <select
                        name="service_type"
                        class="form-select">

                        <option value="internal">

                            Interni

                        </option>

                        <option value="external">

                            Eksterni

                        </option>

                        <option value="onsite">

                            Intervencija na licu mesta

                        </option>

                        <option value="warranty">

                            Garancija

                        </option>

                    </select>

                </div>

                <div class="col-12">

                    <label class="form-label">

                        Naslov problema

                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        required>

                </div>

                <div class="col-12">

                    <label class="form-label">

                        Opis problema

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5"></textarea>

                </div>

                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-danger">

                        Pošalji u servis

                    </button>

                </div>

            </div>

        </form>

    </div>

</main>

<?php include "../../../layouts/footer.php"; ?>