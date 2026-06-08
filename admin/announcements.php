<?php

$pageTitle = "Obaveštenja";

include "../layouts/layout_start.php";

require_login();
require_role(['admin']);

$result = $conn->query("
    SELECT *
    FROM announcements
    ORDER BY created_at DESC
");
?>

<div class="page-card">

    <div class="d-flex justify-content-between mb-3">

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#announcementModal">

            Novo obaveštenje

        </button>

    </div>

    <table class="table table-hover">

        <thead>

            <tr>
                <th>Naslov</th>
                <th>Prioritet</th>
                <th>Aktivno</th>
                <th>Važi do</th>
                <th>Akcije</th>
            </tr>

        </thead>

        <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= e($row['title']) ?>
                    </td>

                    <td>

                        <?php

                        $badge = match ($row['priority']) {
                            'danger' => 'bg-danger',
                            'warning' => 'bg-warning text-dark',
                            default => 'bg-info'
                        };

                        ?>

                        <span class="badge <?= $badge ?>">
                            <?= e($row['priority']) ?>
                        </span>

                    </td>

                    <td>

                        <?php if ($row['active']): ?>

                            <span class="badge bg-success">
                                Aktivno
                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">
                                Neaktivno
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?= e($row['end_date'] ?? '-') ?>

                    </td>

                    <td class="text-end">

                        <button
                            class="btn btn-sm btn-primary">

                            Izmeni

                        </button>

                        <button
                            class="btn btn-sm btn-danger">

                            Obriši

                        </button>

                    </td>

                </tr>

            <?php endwhile; ?>

        </tbody>

    </table>

</div>

<div
    class="modal fade"
    id="announcementModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="<?= url('admin/actions/announcement_create.php') ?>">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Novo obaveštenje

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Naslov

                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Poruka

                        </label>

                        <textarea
                            name="message"
                            rows="5"
                            class="form-control"
                            required></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Prioritet

                        </label>

                        <select
                            name="priority"
                            class="form-select">

                            <option value="info">
                                Informacija
                            </option>

                            <option value="warning">
                                Upozorenje
                            </option>

                            <option value="danger">
                                Kritično
                            </option>

                        </select>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <label class="form-label">
                                Početak
                            </label>

                            <input
                                type="text"
                                name="start_date"
                                class="form-control datetimepicker"
                                placeholder="dd.mm.gggg hh:mm">
                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Kraj
                            </label>

                            <input
                                type="text"
                                name="end_date"
                                class="form-control datetimepicker"
                                placeholder="dd.mm.gggg hh:mm">

                        </div>

                    </div>

                    <div class="form-check mt-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="active"
                            id="active"
                            checked>

                        <label
                            class="form-check-label"
                            for="active">

                            Aktivno obaveštenje

                        </label>

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


<?php include "../layouts/layout_end.php"; ?>