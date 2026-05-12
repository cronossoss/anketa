<div
    class="modal fade"
    id="employeeViewModal"
    tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content border-0 shadow">

            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="employeeViewModalTitle">

                    Pregled zaposlenog

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <!-- BODY -->

            <div class="modal-body">

                <!-- PROFILE -->

                <div class="employee-profile-header border-bottom pb-3 mb-4">

                    <div class="row align-items-center g-3">

                        <!-- PHOTO -->

                        <div class="col-md-2 text-center">

                            <img
                                src="<?= BASE_URL ?>assets/images/default-user.png"
                                id="viewEmployeePhoto"
                                class="rounded-circle shadow-sm"
                                style="
                                    width: 110px;
                                    height: 110px;
                                    object-fit: cover;
                                ">

                        </div>

                        <!-- INFO -->

                        <div class="col-md-10">

                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">

                                <h3
                                    class="mb-0 fw-bold"
                                    id="viewEmployeeName">

                                    -

                                </h3>

                                <span
                                    class="badge bg-secondary"
                                    id="viewEmployeeManagerBadge">

                                    Zaposleni

                                </span>

                            </div>

                            <div class="text-muted mb-1">

                                <span id="viewEmployeePid">
                                    -
                                </span>

                                •

                                <span id="viewEmployeeOj">
                                    -
                                </span>

                            </div>

                            <div
                                class="text-muted"
                                id="viewEmployeePosition">

                                -

                            </div>

                        </div>

                    </div>

                </div>

                <!-- LIČNI PODACI -->

                <div class="card border-0 shadow-sm mb-3">

                    <div class="card-body">

                        <h5 class="fs-6 mb-3">
                            Lični podaci
                        </h5>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    JMBG
                                </small>

                                <div id="viewEmployeeJmbg">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Datum rođenja
                                </small>

                                <div id="viewEmployeeBirthDate">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Adresa
                                </small>

                                <div id="viewEmployeeAddress">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Privatni telefon
                                </small>

                                <div id="viewEmployeePhone">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Email
                                </small>

                                <div id="viewEmployeeEmail">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Tekući račun
                                </small>

                                <div id="viewEmployeeBank">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- POSLOVNI PODACI -->

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="fs-6 mb-3">
                            Poslovni podaci
                        </h5>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Organizaciona jedinica
                                </small>

                                <div id="viewEmployeeUnit">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Pozicija
                                </small>

                                <div id="viewEmployeeWorkPosition">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Matični broj
                                </small>

                                <div id="viewEmployeePersonalId">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Datum zaposlenja
                                </small>

                                <div id="viewEmployeeHireDate">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Vrsta ugovora
                                </small>

                                <div id="viewEmployeeContractType">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Istek ugovora
                                </small>

                                <div id="viewEmployeeContractEnd">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Službeni email
                                </small>

                                <div id="viewEmployeeBusinessEmail">
                                    -
                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Službeni telefon
                                </small>

                                <div id="viewEmployeeBusinessPhone">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Zatvori

                </button>

            </div>

        </div>

    </div>

</div>