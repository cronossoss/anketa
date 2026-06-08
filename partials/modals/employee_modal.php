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
                action="<?= url('admin/actions/employees_create.php') ?>">

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
                        class="employee-close-btn"
                        data-bs-dismiss="modal">

                        <i class="bi bi-x-lg"></i>
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
                                    src="<?= url('assets/images/default-user.png') ?>"
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
                                        id="employeeDisplayRoleBadge">

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

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Godišnji odmor
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="annual_leave_days"
                                        name="annual_leave_days"
                                        min="0"
                                        max="60"
                                        value="20">

                                </div>

                                <div class="card mt-4">

                                    <div class="card-header">

                                        <h5 class="mb-0">

                                            Zadužen inventar

                                        </h5>

                                    </div>

                                    <div class="card-body p-0">

                                        <div id="employeeAssetsContainer">

                                            <div class="p-3 text-muted">

                                                Učitavanje inventara...

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <div class="form-check mt-1">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="is_manager"
                                            id="is_manager">

                                        <label class="form-check-label small">

                                            Rukovodilac

                                        </label>

                                    </div>

                                </div>

                                <div class="col-md-12 mt-2">

                                    <hr>

                                    <h6 class="small text-muted mb-2">
                                        Pristup aplikaciji
                                    </h6>

                                </div>

                                <div class="col-xl-4 col-md-6">

                                    <div class="form-check mt-1">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="has_account"
                                            id="has_account">

                                        <label class="form-check-label small">

                                            Korisnik aplikacije

                                        </label>

                                    </div>

                                </div>

                                <div
                                    class="col-xl-4 col-md-6"
                                    id="system_role_wrapper"
                                    style="display:none;">

                                    <label class="form-label small mb-1">
                                        Sistemska uloga
                                    </label>

                                    <select
                                        class="form-select form-select-sm"
                                        name="system_role"
                                        id="system_role">

                                        <option value="">
                                            -- Izaberi --
                                        </option>

                                        <option value="admin">
                                            Administrator
                                        </option>

                                        <option value="hr">
                                            HR
                                        </option>

                                        <option value="manager">
                                            Manager
                                        </option>

                                        <option value="it">
                                            IT
                                        </option>

                                        <option value="user">
                                            Korisnik
                                        </option>

                                    </select>

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

        if (!dateString) {
            return '-';
        }

        const parts = dateString.split('-');

        if (parts.length !== 3) {
            return dateString;
        }

        return parts[2] + '.' + parts[1] + '.' + parts[0];
    }

    document.addEventListener('DOMContentLoaded', function() {

        const employeeViewModal =
            document.getElementById(
                'employeeViewModal'
            );

        if (!employeeViewModal) {
            return;
        }

        employeeViewModal.addEventListener(
            'show.bs.modal',
            async function(event) {

                const button =
                    event.relatedTarget;

                if (!button) {
                    return;
                }

                let employee = {};

                try {

                    employee = JSON.parse(
                        button.getAttribute(
                            'data-employee'
                        )
                    );

                    console.log(employee);

                } catch (e) {

                    console.error(
                        'Greška pri parsiranju zaposlenog:',
                        e
                    );

                    return;
                }

                //
                // BASIC
                //

                setText(
                    'employee_view_name',
                    (
                        (employee.first_name || '') +
                        ' ' +
                        (employee.last_name || '')
                    ).trim()
                );

                setText(
                    'employee_view_personal_number',
                    employee.personal_id
                );

                setText(
                    'employee_view_organization',
                    employee.organizational_unit_name
                );

                setText(
                    'employee_view_position',
                    employee.position
                );

                setText(
                    'employee_view_email',
                    employee.business_email
                );

                setText(
                    'employee_view_phone',
                    employee.business_phone
                );

                //
                // PHOTO
                //

                const photo =
                    document.getElementById(
                        'employee_view_photo'
                    );

                if (photo) {

                    photo.src =
                        employee.photo ?
                        employee.photo :
                        '/anketa/assets/img/default-user.png';
                }

                //
                // BADGE
                //

                const roleBadge =
                    document.getElementById(
                        'employee_view_role_badge'
                    );

                if (roleBadge) {

                    if (employee.is_manager == 1) {

                        roleBadge.innerHTML =
                            'Rukovodilac';

                        roleBadge.className =
                            'badge bg-warning text-dark px-3 py-2';

                    } else {

                        roleBadge.innerHTML =
                            'Zaposleni';

                        roleBadge.className =
                            'badge bg-secondary px-3 py-2';
                    }
                }

                //
                // PERSONAL
                //

                setText(
                    'employee_view_jmbg',
                    employee.jmbg
                );

                setText(
                    'employee_view_birth_date',
                    formatDate(
                        employee.birth_date
                    )
                );

                setText(
                    'employee_view_private_phone',
                    employee.phone_private
                );

                setText(
                    'employee_view_private_email',
                    employee.email
                );

                setText(
                    'employee_view_address',
                    employee.address
                );

                setText(
                    'employee_view_bank_account',
                    employee.bank_account
                );

                //
                // BUSINESS
                //

                setText(
                    'employee_view_org_unit',
                    employee.organizational_unit_name
                );

                setText(
                    'employee_view_job_position',
                    employee.position
                );

                setText(
                    'employee_view_employee_number',
                    employee.personal_id
                );

                setText(
                    'employee_view_employment_date',
                    formatDate(
                        employee.hire_date
                    )
                );

                setText(
                    'employee_view_contract_type',
                    employee.contract_type
                );

                setText(
                    'employee_view_contract_expiry',
                    formatDate(
                        employee.contract_end
                    )
                );

                setText(
                    'employee_view_business_email',
                    employee.business_email
                );

                setText(
                    'employee_view_business_phone',
                    employee.business_phone
                );

                //
                // STATUS
                //

                const statusBadge =
                    document.getElementById(
                        'employee_status_badge'
                    );

                if (statusBadge) {

                    if (
                        employee.status === 'inactive'
                    ) {

                        statusBadge.className =
                            'badge bg-danger';

                        statusBadge.innerHTML =
                            'Neaktivan';

                    } else {

                        statusBadge.className =
                            'badge bg-success';

                        statusBadge.innerHTML =
                            'Aktivan';
                    }
                }

                //
                // LAST ACTIVITY
                //

                setText(
                    'employee_last_activity',
                    employee.last_activity ||
                    'Nema aktivnosti'
                );

                //
                // LOAD ASSETS
                //

                await loadEmployeeAssets(
                    employee.id
                );

                //
                // HISTORY PLACEHOLDER
                //

                loadEmployeeHistory(
                    employee.id
                );

                //
                // TIMELINE PLACEHOLDER
                //

                loadEmployeeTimeline(
                    employee.id
                );
            }
        );

        //
        // EDIT BUTTON
        //

        const editBtn =
            document.getElementById(
                'btn_edit_employee'
            );

        if (editBtn) {

            editBtn.addEventListener(
                'click',
                function() {

                    console.log(
                        'Otvaranje edit modala'
                    );
                }
            );
        }
    });

    //
    // HELPERS
    //

    function setText(id, value) {

        const element =
            document.getElementById(id);

        if (!element) {
            return;
        }

        element.innerText =
            value && value !== 'null' ?
            value :
            '-';
    }

    //
    // LOAD EMPLOYEE ASSETS
    //

    async function loadEmployeeAssets(
        employeeId
    ) {

        const tbody =
            document.getElementById(
                'employee_assets_table'
            );

        if (!tbody) {
            return;
        }

        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center p-4 text-muted">
                    Učitavanje inventara...
                </td>
            </tr>
        `;

        try {

            const response =
                await fetch(
                    APP.baseUrl +
                    'modules/assets/api/get_employee_assets.php?employee_id=' +
                    employeeId
                );

            const assets =
                await response.json();

            console.log(assets);

            //
            // COUNT
            //

            const countElement =
                document.getElementById(
                    'employee_assets_count'
                );

            if (countElement) {

                countElement.innerText =
                    assets.length;
            }

            //
            // EMPTY
            //

            if (!assets.length) {

                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center p-4 text-muted">
                            Zaposleni nema zadužen inventar.
                        </td>
                    </tr>
                `;

                return;
            }

            //
            // TABLE
            //

            let html = '';

            assets.forEach(asset => {

                html += `
                    <tr>

                        <td>
                            ${asset.category_name ?? '-'}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                ${asset.manufacturer ?? ''}
                                ${asset.model ?? ''}
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                ${asset.inventory_number ?? '-'}
                            </span>
                        </td>

                        <td>

                            <span class="badge bg-success">
                                Zadužen
                            </span>

                        </td>

                        <td>

                            <div class="d-flex gap-2">

                                <button
                                    class="btn btn-sm btn-outline-primary">

                                    Pregled
                                </button>

                                <button
                                    class="btn btn-sm btn-outline-danger">

                                    Razduži
                                </button>

                            </div>

                        </td>

                    </tr>
                `;
            });

            tbody.innerHTML = html;

        } catch (error) {

            console.error(error);

            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center p-4 text-danger">
                        Greška pri učitavanju inventara.
                    </td>
                </tr>
            `;
        }
    }

    //
    // HISTORY
    //

    function loadEmployeeHistory(
        employeeId
    ) {

        const container =
            document.getElementById(
                'employee_history_container'
            );

        if (!container) {
            return;
        }

        container.innerHTML = `
            <div class="text-muted">
                Istorija će biti uskoro implementirana.
            </div>
        `;
    }

    //
    // TIMELINE
    //

    function loadEmployeeTimeline(
        employeeId
    ) {

        const container =
            document.getElementById(
                'employee_timeline'
            );

        if (!container) {
            return;
        }

        container.innerHTML = `
            <div class="timeline-item border-start ps-3 mb-3">

                <div class="small text-muted">
                    ${new Date().toLocaleDateString()}
                </div>

                <div class="fw-semibold">
                    Otvoren profil zaposlenog
                </div>

            </div>
        `;
    }
</script>