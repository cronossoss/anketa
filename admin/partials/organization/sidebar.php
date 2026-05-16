<?php

/** @var array $units */

?>

<!-- RIGHT -->

<div class="col-lg-4">

    <!-- NEW UNIT -->

    <div class="page-card mb-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h4 class="mb-0">
                Nova jedinica
            </h4>

            <button
                type="button"
                class="btn btn-primary btn-sm"
                id="addOrganizationBtn">

                + Dodaj

            </button>

        </div>

        <div class="text-muted small">

            Dodavanje organizacionih jedinica
            i osnovnih podataka.

        </div>

    </div>

    <!-- RELATIONS -->

    <div class="page-card">

        <h4 class="mb-4">
            Povezivanje jedinica
        </h4>

        <form
            method="POST"
            action="<?= url('admin/actions/organization_relation_create.php') ?>">

            <input
                type="hidden"
                name="csrf"
                value="<?= csrf_token() ?>">

            <div class="mb-3">

                <label class="form-label">
                    Nadređena jedinica
                </label>

                <select
                    name="parent_id"
                    class="form-select"
                    required>

                    <?php foreach ($units as $u): ?>

                        <option value="<?= $u['id'] ?>">

                            <?= e($u['code']) ?>
                            -
                            <?= e($u['name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Podređena jedinica
                </label>

                <select
                    name="child_id"
                    class="form-select"
                    required>

                    <?php foreach ($units as $u): ?>

                        <option value="<?= $u['id'] ?>">

                            <?= e($u['code']) ?>
                            -
                            <?= e($u['name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Tip relacije
                </label>

                <select
                    name="relation_type"
                    class="form-select">

                    <option value="organizational">
                        Organizaciona
                    </option>

                    <option value="hierarchical">
                        Hijerarhijska
                    </option>

                </select>

            </div>

            <button class="btn btn-dark w-100">

                Poveži jedinice

            </button>

        </form>

    </div>

</div>