<?php

require_once '../../../config/init.php';

require_login();
require_role(['manager', 'admin']);

$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

if (!$id || !in_array($type, ['absence', 'exit'])) {

    header('Location: index.php');
    exit;
}

$pageTitle = 'Odbijanje zahteva';

include "../../../layouts/layout_start.php";
?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Odbijanje zahteva

            </h4>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="reject_save.php">

                <input
                    type="hidden"
                    name="type"
                    value="<?= htmlspecialchars($type) ?>">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $id ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="mb-3">

                    <label class="form-label">

                        Razlog odbijanja

                    </label>

                    <textarea
                        name="reason"
                        rows="5"
                        class="form-control"
                        required></textarea>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Otkaži

                    </a>

                    <button
                        type="submit"
                        class="btn btn-danger">

                        <i class="bi bi-x-lg me-1"></i>

                        Odbij zahtev

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/layout_end.php"; ?>