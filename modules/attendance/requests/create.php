<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Novi zahtev';

include "../../../layouts/admin_layout_start.php";

//
// TIPOVI ODSUSTVA
//

$types = $conn->query("

    SELECT
        id,
        name

    FROM attendance_absence_types

    WHERE active = 1

    ORDER BY name ASC

");

?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h4 class="mb-4">

                Novi zahtev za odsustvo

            </h4>

            <form
                method="POST"
                action="store.php">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">

                            Tip odsustva

                        </label>

                        <select
                            name="absence_type_id"
                            class="form-select"
                            required>

                            <option value="">

                                Izaberi

                            </option>

                            <?php while ($type = $types->fetch_assoc()): ?>

                                <option
                                    value="<?= $type['id'] ?>">

                                    <?= htmlspecialchars(
                                        $type['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Datum i vreme od

                        </label>

                        <input
                            type="datetime-local"
                            name="date_from"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Datum i vreme do

                        </label>

                        <input
                            type="datetime-local"
                            name="date_to"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-12">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="4"></textarea>

                    </div>

                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-send"></i>

                            Pošalji zahtev

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>