<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Novi zahtev';

include "../../../layouts/admin_layout_start.php";

$exitTypes = $conn->query("
    SELECT id, name
    FROM attendance_exit_types
    WHERE active = 1
      AND id IN (2,3,4)
    ORDER BY name
");

$absenceTypes = $conn->query("
    SELECT id, name
    FROM attendance_absence_types
    WHERE id IN (6,8,10,12)
    ORDER BY name
");
?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">
                Podnošenje zahteva
            </h4>
        </div>

        <div class="card-body">

            <?php if (!empty($_SESSION['error'])): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($_SESSION['error']) ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>

                <div class="alert alert-success">

                    <?= htmlspecialchars($_SESSION['success']) ?>

                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <form
                method="POST"
                action="store.php"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Vrsta zahteva
                        </label>

                        <select
                            id="request_kind"
                            name="request_kind"
                            class="form-select"
                            required>

                            <option value="absence" selected>
                                Odsustvo
                            </option>

                            <option value="exit">
                                Izlaznica
                            </option>

                        </select>

                    </div>

                </div>

                <div
                    id="exit-block"
                    class="row g-3 mt-1 d-none">

                    <div class="col-md-4">

                        <label class="form-label">
                            Tip izlaznice
                        </label>

                        <select
                            name="exit_type_id"
                            class="form-select">

                            <option value="">
                                Izaberite
                            </option>

                            <?php while ($row = $exitTypes->fetch_assoc()): ?>

                                <option value="<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Od
                        </label>

                        <input
                            type="text"
                            name="exit_from"
                            class="form-control sr-datetime">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Do
                        </label>

                        <input
                            type="text"
                            name="exit_to"
                            class="form-control sr-datetime">

                    </div>

                </div>

                <div
                    id="absence-block"
                    class="row g-3 mt-1 d-none">

                    <div class="col-md-4">

                        <label class="form-label">
                            Tip odsustva
                        </label>

                        <select
                            id="absence_type_id"
                            name="absence_type_id"
                            class="form-select">

                            <option value="">
                                Izaberite
                            </option>

                            <?php while ($row = $absenceTypes->fetch_assoc()): ?>

                                <option value="<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Od datuma
                        </label>

                        <input
                            type="text"
                            name="absence_from"
                            class="form-control sr-date">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Do datuma
                        </label>

                        <input
                            type="text"
                            name="absence_to"
                            class="form-control sr-date">

                    </div>

                </div>



                <div
                    id="paid-leave-block"
                    class="row g-3 mt-1 d-none">

                    <div class="col-md-6">

                        <label class="form-label">
                            Razlog
                        </label>

                        <select
                            id="paid_leave_reason"
                            name="paid_leave_reason"
                            class="form-select">

                            <option value="">
                                Izaberite
                            </option>

                            <option value="Rođenje deteta">
                                Rođenje deteta
                            </option>

                            <option value="Smrtni slučaj">
                                Smrtni slučaj
                            </option>

                            <option value="Preseljenje">
                                Preseljenje
                            </option>

                            <option value="Polaganje ispita">
                                Polaganje ispita
                            </option>

                            <option value="Krsna slava">
                                Krsna slava
                            </option>

                            <option value="Dobrovoljno davanje krvi">
                                Dobrovoljno davanje krvi
                            </option>

                            <option value="Drugo">
                                Drugo
                            </option>

                        </select>

                    </div>

                    <div
                        id="other-reason-block"
                        class="col-md-6 d-none">

                        <label class="form-label">
                            Obrazloženje
                        </label>

                        <input
                            type="text"
                            name="other_reason"
                            class="form-control">

                    </div>

                </div>

                <div class="row g-3 mt-1">

                    <div class="col-12">

                        <label class="form-label">
                            Napomena
                        </label>

                        <textarea
                            name="note"
                            rows="4"
                            class="form-control"></textarea>

                    </div>

                    <div class="col-12">

                        <label class="form-label">
                            Dokument
                        </label>

                        <input
                            type="file"
                            name="document"
                            class="form-control">

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Otkaži

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-send me-1"></i>

                        Pošalji zahtev

                    </button>

                </div>

            </form>



        </div>

    </div>

</div>

<script>
    const requestKind =
        document.getElementById('request_kind');

    const absenceType =
        document.getElementById('absence_type_id');

    const paidReason =
        document.getElementById('paid_leave_reason');

    requestKind.addEventListener('change', function() {

        document
            .getElementById('exit-block')
            .classList.add('d-none');

        document
            .getElementById('absence-block')
            .classList.add('d-none');

        document
            .getElementById('paid-leave-block')
            .classList.add('d-none');

        document
            .getElementById('other-reason-block')
            .classList.add('d-none');

        if (this.value === 'exit') {

            document
                .getElementById('exit-block')
                .classList.remove('d-none');
        }

        if (this.value === 'absence') {

            document
                .getElementById('absence-block')
                .classList.remove('d-none');
        }

    });

    absenceType.addEventListener('change', function() {

        const paidBlock =
            document.getElementById('paid-leave-block');

        if (this.value == '6') {

            paidBlock.classList.remove('d-none');

        } else {

            paidBlock.classList.add('d-none');
        }

    });

    paidReason.addEventListener('change', function() {

        const otherBlock =
            document.getElementById('other-reason-block');

        if (this.value === 'Drugo') {

            otherBlock.classList.remove('d-none');

        } else {

            otherBlock.classList.add('d-none');
        }

    });

    requestKind.dispatchEvent(
        new Event('change')
    );
</script>

<?php include "../../../layouts/admin_layout_end.php"; ?>