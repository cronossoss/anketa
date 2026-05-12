<div
    class="modal fade"
    id="typeModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                id="typeForm"
                action="<?= BASE_URL ?>modules/assets/actions/asset_types_create.php">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="id"
                    id="type_id">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="typeModalTitle">

                        Dodavanje tipa

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

                            Naziv

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            id="type_name"
                            required>

                    </div>

                    <div>

                        <label class="form-label">

                            Šifra

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="code"
                            id="type_code">

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Zatvori

                    </button>

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