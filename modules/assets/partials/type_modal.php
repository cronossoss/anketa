<div
    class="modal fade"
    id="typeModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/asset_types_create.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Dodaj tip inventara

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

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

<div
    class="modal fade"
    id="editTypeModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/asset_types_update.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="id"
                    id="edit_type_id">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Izmena tipa

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Naziv

                        </label>

                        <input
                            type="text"
                            name="name"
                            id="edit_type_name"
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
                            id="edit_type_code"
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