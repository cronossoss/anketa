<div
    class="modal fade"
    id="categoryModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                id="categoryForm"
                action="<?= BASE_URL ?>modules/assets/actions/asset_categories_create.php">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="id"
                    id="category_id">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="categoryModalTitle">

                        Dodavanje kategorije

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
                            id="category_name"
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
                            id="category_code">

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