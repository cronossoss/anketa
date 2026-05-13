<!-- CREATE -->

<div
    class="modal fade"
    id="attributeModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/asset_attribute_create.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Dodaj atribut

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Kategorija

                            </label>

                            <select
                                name="category_id"
                                class="form-select"
                                required>

                                <option value="">
                                    Izaberi kategoriju
                                </option>

                                <?php mysqli_data_seek($categories, 0); ?>

                                <?php while ($category = $categories->fetch_assoc()): ?>

                                    <option value="<?= $category['id'] ?>">

                                        <?= e($category['name']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Naziv

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Šifra

                            </label>

                            <input
                                type="text"
                                name="code"
                                class="form-control">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Tip polja

                            </label>

                            <select
                                name="field_type"
                                class="form-select"
                                required>

                                <option value="text">Text</option>

                                <option value="number">Number</option>

                                <option value="date">Date</option>

                                <option value="textarea">Textarea</option>

                                <option value="select">Select</option>

                                <option value="checkbox">Checkbox</option>

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Opcije
                            (jedna po liniji)

                        </label>

                        <textarea
                            name="options"
                            class="form-control"
                            rows="4"></textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Sort order

                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                class="form-control"
                                value="0">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label d-block">

                                Required

                            </label>

                            <input
                                type="checkbox"
                                name="is_required"
                                value="1">

                        </div>

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