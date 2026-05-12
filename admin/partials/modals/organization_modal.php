<div
    class="modal fade"
    id="organizationModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                id="organizationForm">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="organizationModalTitle">>
                        Organizacija
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= csrf_token() ?>">

                    <input
                        type="hidden"
                        name="id"
                        id="org_id">

                    <div class="mb-3">

                        <label class="form-label">
                            Tip
                        </label>

                        <select
                            name="type"
                            id="org_type"
                            class="form-select">

                            <option value="OC">OC</option>
                            <option value="OJ">OJ</option>
                            <option value="OD">OD</option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Šifra
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="org_code"
                            class="form-control">

                            <div
                                class="mb-3"
                                id="odCodeWrapper">

                                <label class="form-label">
                                    OD šifra
                                </label>

                                <input
                                    type="text"
                                    name="od_code"
                                    id="org_od_code"
                                    class="form-control">

                            </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Naziv
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="org_name"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Opis
                        </label>

                        <textarea
                            name="description"
                            id="org_description"
                            class="form-control"></textarea>

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