<?php

/** @var mysqli_result $units */ ?>
<div
    class="modal fade"
    id="employeeModal"
    tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <form
                method="POST"
                id="employeeForm"
                action="<?= BASE_URL ?>admin/actions/employees_create.php">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="employee_id"
                    id="employee_id">

                <!-- HEADER -->

                <div class="modal-header py-2">

                    <h5
                        class="modal-title fs-6"
                        id="employeeModalTitle">

                        Dodavanje zaposlenog

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- BODY -->

                <div class="modal-body p-3">

                    <!-- PROFILE HEADER -->

                    <div class="employee-profile-header border-bottom pb-3 mb-3">

                        <div class="row align-items-center g-3">

                            <!-- PHOTO -->

                            <div class="col-md-2 text-center">

                                <img
                                    src="<?= BASE_URL ?>assets/images/default-user.png"
                                    id="employeePhotoPreview"
                                    class="rounded-circle shadow-sm"
                                    style="
                                        width: 100px;
                                        height: 100px;
                                        object-fit: cover;
                                    ">

                            </div>

                            <!-- INFO -->

                            <div class="col-md-10">

                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">

                                    <h3
                                        class="mb-0 fw-bold"
                                        id="employeeDisplayName"
                                        style="
                                            font-size: 30px;
                                            line-height: 1.1;
                                        ">

                                        Novi zaposleni

                                    </h3>

                                    <span
                                        class="badge bg-secondary"
                                        id="employeeManagerBadge">

                                        Zaposleni

                                    </span>

                                </div>

                                <!-- SUB INFO -->

                                <div class="text-muted small mb-1">

                                    <span id="employeeDisplayPid">
                                        -
                                    </span>

                                    •

                                    <span id="employeeDisplayOj">
                                        Organizacija nije dodeljena
                                    </span>

                                </div>

                                <!-- POSITION -->

                                <div
                                    class="text-muted"
                                    id="employeeDisplayPosition"
                                    style="font-size:14px;">

                                    Pozicija nije definisana

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- LIČNI PODACI -->

                    <div class="card border-0 shadow-sm mb-3">

                        <div class="card-body p-3">

                            <h5 class="mb-3 fs-6">
                                Lični podaci
                            </h5>

                            <div class="row g-2">

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Ime
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="first_name"
                                        id="first_name"
                                        required>

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Prezime
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="last_name"
                                        id="last_name"
                                        required>

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        JMBG
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="jmbg"
                                        id="jmbg">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Datum rođenja
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm datepicker"
                                        name="birth_date"
                                        id="birth_date"
                                        placeholder="dd.mm.gggg">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Adresa
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="address"
                                        id="address">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Telefon
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="phone_private"
                                        id="phone_private">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control form-control-sm"
                                        name="email"
                                        id="email">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Tekući račun
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="bank_account"
                                        id="bank_account">

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- POSLOVNI PODACI -->

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-3">

                            <h5 class="mb-3 fs-6">
                                Poslovni podaci
                            </h5>

                            <div class="row g-2">

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Organizaciona jedinica
                                    </label>

                                    <select
                                        class="form-select form-select-sm"
                                        name="organizational_unit_id"
                                        id="organizational_unit_id"
                                        required>

                                        <option value="">
                                            -- Izaberi --
                                        </option>

                                        <?php while ($u = $units->fetch_assoc()): ?>

                                            <option value="<?= $u['id'] ?>">

                                                (<?= e($u['code']) ?>)
                                                <?= e($u['name']) ?>

                                            </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Pozicija
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="position"
                                        id="position">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Matični broj
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="personal_id"
                                        id="personal_id">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Datum zaposlenja
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm datepicker"
                                        name="hire_date"
                                        id="hire_date"
                                        placeholder="dd.mm.gggg">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Vrsta ugovora
                                    </label>

                                    <select
                                        class="form-select form-select-sm"
                                        name="contract_type"
                                        id="contract_type">

                                        <option value="">
                                            -- Izaberi --
                                        </option>

                                        <option value="Na neodređeno">
                                            Na neodređeno
                                        </option>

                                        <option value="Na određeno">
                                            Na određeno
                                        </option>

                                        <option value="Privremeni i povremeni poslovi">
                                            Privremeni i povremeni poslovi
                                        </option>

                                        <option value="Ugovor o delu">
                                            Ugovor o delu
                                        </option>

                                        <option value="Studentska zadruga">
                                            Studentska zadruga
                                        </option>

                                        <option value="Praksa">
                                            Praksa
                                        </option>

                                        <option value="Volonter">
                                            Volonter
                                        </option>

                                    </select>

                                </div>

                                <div
                                    class="col-xl-4 col-md-6"
                                    id="contract_end_wrapper">

                                    <label class="form-label small mb-1">
                                        Istek ugovora
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm datepicker"
                                        name="contract_end"
                                        id="contract_end"
                                        placeholder="dd.mm.gggg">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Službeni email
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control form-control-sm"
                                        name="business_email"
                                        id="business_email">

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <label class="form-label small mb-1">
                                        Službeni telefon
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        name="business_phone"
                                        id="business_phone">

                                </div>

                                <div class="col-md-12">

                                    <div class="form-check mt-1">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="is_manager"
                                            id="is_manager">

                                        <label
                                            class="form-check-label small">

                                            Rukovodilac

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->

                <div class="modal-footer py-2">

                    <button
                        type="button"
                        class="btn btn-sm btn-secondary"
                        data-bs-dismiss="modal">

                        Zatvori

                    </button>

                    <button
                        type="submit"
                        class="btn btn-sm btn-primary">

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
    function formatDate(dateString) {

        if (!dateString) return '';

        const parts = dateString.split('-');

        return parts[2] + '.' + parts[1] + '.' + parts[0];
    }

    document.addEventListener('DOMContentLoaded', function() {

        const employeeModal = document.getElementById('employeeModal');

        employeeModal.addEventListener('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            // CREATE MODE
            if (button && button.id === 'addEmployeeBtn') {

                // RESET ONLY CREATE VALUES

                document.getElementById('employee_id').value = '';

                document.getElementById('first_name').value = '';
                document.getElementById('last_name').value = '';
                document.getElementById('jmbg').value = '';
                document.getElementById('birth_date').value = '';

                document.getElementById('address').value = '';
                document.getElementById('phone_private').value = '';
                document.getElementById('email').value = '';
                document.getElementById('bank_account').value = '';

                document.getElementById('organizational_unit_id').value = '';
                document.getElementById('position').value = '';
                document.getElementById('personal_id').value = '';
                document.getElementById('hire_date').value = '';
                document.getElementById('contract_type').value = '';
                document.getElementById('contract_end').value = '';
                document.getElementById('business_email').value = '';
                document.getElementById('business_phone').value = '';

                document.getElementById('is_manager').checked = false;

                // HEADER

                document.getElementById('employeeDisplayName').innerText =
                    'Novi zaposleni';

                document.getElementById('employeeDisplayPid').innerText =
                    '-';

                document.getElementById('employeeDisplayOj').innerText =
                    'Organizacija nije dodeljena';

                document.getElementById('employeeDisplayPosition').innerText =
                    'Pozicija nije definisana';

                toggleContractEnd();

            }

        });

        // CONTRACT TYPE

        const contractType = document.getElementById('contract_type');
        const contractEndWrapper = document.getElementById('contract_end_wrapper');

        function toggleContractEnd() {

            if (contractType.value === 'Na neodređeno') {

                contractEndWrapper.style.display = 'none';

                document.getElementById('contract_end').value = '';

            } else {

                contractEndWrapper.style.display = 'block';

            }
        }

        contractType.addEventListener('change', toggleContractEnd);

        toggleContractEnd();

    });
</script>