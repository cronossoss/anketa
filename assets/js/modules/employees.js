document.addEventListener('DOMContentLoaded', () => {
    initEmployeeSearch();

    initEditButtons();

    initCreateButton();

    initDatepickers();

    initViewButtons();

    const hasAccount = document.getElementById('has_account');

    if (hasAccount) {
        hasAccount.addEventListener('change', toggleSystemRole);
    }
});

/* =========================
   DATEPICKER
========================= */

function initDatepickers() {
    document.querySelectorAll('.datepicker').forEach((el) => {
        flatpickr(el, {
            locale: 'sr',

            dateFormat: 'Y-m-d',

            altInput: true,

            altFormat: 'd.m.Y',

            allowInput: true,
        });
    });
}

/* =========================
   HELPERS
========================= */

function setValue(id, value) {
    const element = document.getElementById(id);

    if (!element) return;

    element.value = value ?? '';
}

function setDate(id, value) {
    const element = document.getElementById(id);

    if (!element) return;

    const formatted = value ? value.split(' ')[0] : '';

    if (element._flatpickr) {
        element._flatpickr.setDate(formatted, true);
    } else {
        element.value = formatted;
    }
}

function toggleSystemRole() {
    const hasAccount = document.getElementById('has_account');

    const wrapper = document.getElementById('system_role_wrapper');

    if (!hasAccount || !wrapper) return;

    if (hasAccount.checked) {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';

        document.getElementById('system_role').value = '';
    }
}

/* =========================
   SEARCH
========================= */

function initEmployeeSearch() {
    const search = document.getElementById('employeeSearch');

    if (!search) return;

    search.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();

        document.querySelectorAll('.employee-row').forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
}

/* =========================
   CREATE
========================= */

function initCreateButton() {
    const btn = document.getElementById('addEmployeeBtn');

    if (!btn) return;

    btn.addEventListener('click', () => {
        resetEmployeeForm();

        document.getElementById('employeeModalTitle').innerText = 'Dodavanje zaposlenog';

        document.getElementById('employeeForm').action =
            APP.baseUrl + '/admin/actions/employees_create.php';

        document.getElementById('employeePhotoPreview').src =
            APP.baseUrl + '/assets/images/default-user.png';
    });
}

/* =========================
   EDIT
========================= */

function initEditButtons() {
    document.querySelectorAll('.edit-employee-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            resetEmployeeForm();

            const id = btn.dataset.id;

            const response = await fetch(APP.baseUrl + '/admin/actions/employees_get.php?id=' + id);

            const data = await response.json();

            if (data.status !== 'ok') {
                alert('Greška');

                return;
            }

            const e = data.employee;

            console.log(e);

            document.getElementById('employeeDisplayName').innerText =
                e.first_name + ' ' + e.last_name;

            document.getElementById('employeeDisplayPid').innerText = e.personal_id ?? '-';

            document.getElementById('employeeDisplayPosition').innerText = e.position ?? '-';

            const roleBadge = document.getElementById('employeeDisplayRoleBadge');

            if (roleBadge) {
                if (Number(e.is_manager) === 1) {
                    roleBadge.className = 'badge bg-warning text-dark';

                    roleBadge.innerText = 'Rukovodilac';
                } else {
                    roleBadge.className = 'badge bg-secondary';

                    roleBadge.innerText = 'Zaposleni';
                }
            }

            document.getElementById('employeeDisplayOj').innerText =
                '(' + (e.unit_code ?? '-') + ') ' + (e.unit_name ?? '-');

            document.getElementById('employeeModalTitle').innerText = 'Podaci o zaposlenom';

            document.getElementById('employeeForm').action =
                APP.baseUrl + '/admin/actions/employees_update.php';

            setValue('employee_id', e.id);

            setValue('first_name', e.first_name);

            setValue('last_name', e.last_name);

            setValue('position', e.position);

            setValue('email', e.email);

            setValue('personal_id', e.personal_id);

            setValue('jmbg', e.jmbg);

            setValue('address', e.address);

            setValue('phone_private', e.phone_private);

            setValue('bank_account', e.bank_account);

            setValue('business_email', e.business_email);

            setValue('business_phone', e.business_phone);

            setValue('annual_leave_days', e.annual_leave_days);

            setValue('contract_type', e.contract_type);

            setValue('organizational_unit_id', e.organizational_unit_id);

            setDate('birth_date', e.birth_date);

            setDate('hire_date', e.hire_date);

            setDate('contract_end', e.contract_end);

            document.getElementById('is_manager').checked = e.is_manager === 1;

            document.getElementById('has_account').checked = e.has_account === 1;

            document.getElementById('system_role').value = e.system_role || '';

            toggleSystemRole();

            const preview = document.getElementById('employeePhotoPreview');

            if (e.photo) {
                preview.src = APP.baseUrl + '/uploads/employees/' + e.photo;
            } else {
                preview.src = APP.baseUrl + '/assets/images/default-user.png';
            }

            const modalElement = document.getElementById('employeeModal');

            let modal = bootstrap.Modal.getInstance(modalElement);

            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }

            modal.show();

            loadEmployeeAssets(e.id);
        });
    });
}

function initViewButtons() {

    document
        .querySelectorAll('.view-employee-btn')
        .forEach((btn) => {

            btn.addEventListener(
                'click',
                async () => {

                    const id =
                        btn.dataset.id;

                    const response =
                        await fetch(
                            APP.baseUrl +
                                '/admin/actions/employees_get.php?id=' +
                                id
                        );

                    const data =
                        await response.json();

                    if (data.status !== 'ok') {

                        alert('Greška');

                        return;
                    }

                    const e =
                        data.employee;

                    document.getElementById(
                        'viewEmployeeName'
                    ).innerText =
                        e.first_name +
                        ' ' +
                        e.last_name;

                    document.getElementById(
                        'viewEmployeePid'
                    ).innerText =
                        e.personal_id ?? '-';

                    document.getElementById(
                        'viewEmployeeOj'
                    ).innerText =
                        '(' +
                        (e.unit_code ?? '-') +
                        ') ' +
                        (e.unit_name ?? '-');

                    document.getElementById(
                        'viewEmployeePosition'
                    ).innerText =
                        e.position ?? '-';

                    document.getElementById(
                        'viewEmployeeJmbg'
                    ).innerText =
                        e.jmbg ?? '-';

                    document.getElementById(
                        'viewEmployeeBirthDate'
                    ).innerText =
                        e.birth_date ?? '-';

                    document.getElementById(
                        'viewEmployeeAddress'
                    ).innerText =
                        e.address ?? '-';

                    document.getElementById(
                        'viewEmployeePhone'
                    ).innerText =
                        e.phone_private ?? '-';

                    document.getElementById(
                        'viewEmployeeEmail'
                    ).innerText =
                        e.email ?? '-';

                    document.getElementById(
                        'viewEmployeeBank'
                    ).innerText =
                        e.bank_account ?? '-';

                    document.getElementById(
                        'viewEmployeeUnit'
                    ).innerText =
                        e.unit_name ?? '-';

                    document.getElementById(
                        'viewEmployeeWorkPosition'
                    ).innerText =
                        e.position ?? '-';

                    document.getElementById(
                        'viewEmployeePersonalId'
                    ).innerText =
                        e.personal_id ?? '-';

                    document.getElementById(
                        'viewEmployeeHireDate'
                    ).innerText =
                        e.hire_date ?? '-';

                    document.getElementById(
                        'viewEmployeeContractType'
                    ).innerText =
                        e.contract_type ?? '-';

                    document.getElementById(
                        'viewEmployeeContractEnd'
                    ).innerText =
                        e.contract_end ?? '-';

                    document.getElementById(
                        'viewEmployeeBusinessEmail'
                    ).innerText =
                        e.business_email ?? '-';

                    document.getElementById(
                        'viewEmployeeBusinessPhone'
                    ).innerText =
                        e.business_phone ?? '-';

                    document.getElementById(
                        'viewEmployeeAnnualLeave'
                    ).innerText =
                        e.annual_leave_days
                            ? e.annual_leave_days + ' dana'
                            : '-';

                    const roleBadge =
                        document.getElementById(
                            'viewEmployeeRoleBadge'
                        );

                    if (Number(e.is_manager) === 1) {

                        roleBadge.className =
                            'badge bg-warning text-dark';

                        roleBadge.innerText =
                            'Rukovodilac';

                    } else {

                        roleBadge.className =
                            'badge bg-secondary';

                        roleBadge.innerText =
                            'Zaposleni';
                    }

                    const photo =
                        document.getElementById(
                            'viewEmployeePhoto'
                        );

                    if (e.photo) {

                        photo.src =
                            APP.baseUrl +
                            '/uploads/employees/' +
                            e.photo;

                    } else {

                        photo.src =
                            APP.baseUrl +
                            '/assets/images/default-user.png';
                    }

                    const modal =
                        new bootstrap.Modal(
                            document.getElementById(
                                'employeeViewModal'
                            )
                        );

                    modal.show();

                    loadEmployeeAssets(e.id);
                }
            );
        });
}


/* =========================
   RESET FORM
========================= */

function resetEmployeeForm() {
    document.getElementById('employeeForm').reset();

    document.getElementById('employee_id').value = '';
}

async function loadEmployeeAssets(
    employeeId
) {

    const container =
        document.getElementById(
            'viewEmployeeAssetsContainer'
        );

    if (!container) {
        return;
    }

    container.innerHTML = `
        <div class="p-3 text-muted">
            Učitavanje inventara...
        </div>
    `;

    try {

        const response =
            await fetch(
                APP.baseUrl +
                    '/modules/assets/api/get_employee_assets.php?employee_id=' +
                    employeeId
            );

        const assets =
            await response.json();

        if (!assets.length) {

            container.innerHTML = `
                <div class="p-3 text-muted">
                    Zaposleni nema zadužen inventar.
                </div>
            `;

            return;
        }

        let html = `
            <div class="table-responsive">

                <table class="table mb-0">

                    <thead>

                        <tr>

                            <th>Kategorija</th>

                            <th>Uređaj</th>

                            <th>Inventarski broj</th>

                            <th>Zadužen od</th>

                        </tr>

                    </thead>

                    <tbody>
        `;

        assets.forEach(asset => {

            html += `
                <tr>

                    <td>
                        ${asset.category_name ?? ''}
                    </td>

                    <td>
                        ${asset.manufacturer ?? ''}
                        ${asset.model ?? ''}
                    </td>

                    <td>

                        <span class="badge bg-secondary">

                            ${asset.inventory_number ?? ''}

                        </span>

                    </td>

                    <td>
                        ${asset.assigned_at ?? ''}
                    </td>

                </tr>
            `;
        });

        html += `
                    </tbody>

                </table>

            </div>
        `;

        container.innerHTML = html;

    } catch (error) {

        console.error(error);

        container.innerHTML = `
            <div class="p-3 text-danger">
                Greška pri učitavanju inventara.
            </div>
        `;
    }
}
