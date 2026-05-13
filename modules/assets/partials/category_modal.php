<!-- CREATE -->

<div
    class="modal fade"
    id="categoryModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/asset_categories_create.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Dodaj kategoriju

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Tip inventara

                        </label>

                        <select
                            name="asset_type_id"
                            class="form-select"
                            required>

                            <option value="">
                                Izaberi tip
                            </option>

                            <?php mysqli_data_seek($types, 0); ?>

                            <?php while ($type = $types->fetch_assoc()): ?>

                                <option value="<?= $type['id'] ?>">

                                    <?= e($type['name']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Naziv

                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Šifra

                        </label>

                        <input
                            type="text"
                            name="code"
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

<!-- EDIT -->

<div
    class="modal fade"
    id="editCategoryModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/asset_categories_update.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="id"
                    id="edit_category_id">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Izmena kategorije

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Tip inventara

                        </label>

                        <select
                            name="asset_type_id"
                            id="edit_category_type"
                            class="form-select"
                            required>

                            <?php mysqli_data_seek($types, 0); ?>

                            <?php while ($type = $types->fetch_assoc()): ?>

                                <option value="<?= $type['id'] ?>">

                                    <?= e($type['name']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Naziv

                        </label>

                        <input
                            type="text"
                            name="name"
                            id="edit_category_name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Šifra

                        </label>

                        <input
                            type="text"
                            name="code"
                            id="edit_category_code"
                            class="form-control">

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj izmene

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>