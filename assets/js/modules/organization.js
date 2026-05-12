document.addEventListener('DOMContentLoaded', () => {
    initCreateButton();

    initEditButtons();

    toggleOdField();

    initOrganizationEmployeesModal();
});

function initOrganizationEmployeesModal() {
    const cards = document.querySelectorAll('.tree-card');

    if (!cards.length) {
        return;
    }

    cards.forEach((card) => {
        card.addEventListener('click', async function (e) {
            if (e.target.closest('.tree-edit-btn')) {
                return;
            }

            const unitId = this.dataset.unitId;

            const unitName = this.dataset.unitName;

            document.querySelector('#organizationEmployeesModal .modal-title').innerText = unitName;

            const content = document.getElementById('organizationEmployeesContent');

            content.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border"></div>
                    </div>
                    `;

            const modal = new bootstrap.Modal(
                document.getElementById('organizationEmployeesModal'),
            );

            modal.show();

            try {
                const response = await fetch(
                    APP.baseUrl + 'admin/actions/organization_employees.php?unit_id=' + unitId,
                );

                const html = await response.text();

                content.innerHTML = html;

                initEmployeeRows();
            } catch (error) {
                console.error(error);

                content.innerHTML = `
                        <div class="alert alert-danger">
                            Greška pri učitavanju.
                        </div>
                        `;
            }
        });
    });
}

/* =========================
   CREATE
========================= */

function initCreateButton() {
    const btn = document.getElementById('addOrganizationBtn');

    if (!btn) return;

    btn.addEventListener('click', () => {
        document.getElementById('organizationForm').reset();

        document.getElementById('organizationModalTitle').innerText = 'Dodavanje organizacije';

        document.getElementById('organizationForm').action =
            APP.baseUrl + 'admin/actions/organization_create.php';

        const modal = new bootstrap.Modal(document.getElementById('organizationModal'));

        modal.show();
    });
}

/* =========================
   EDIT
========================= */

function initEditButtons() {
    document.querySelectorAll('.edit-unit-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.getElementById('organizationModalTitle').innerText = 'Izmena organizacije';

            document.getElementById('organizationForm').action =
                APP.baseUrl + 'admin/actions/organization_update.php';

            document.getElementById('org_id').value = btn.dataset.id;

            document.getElementById('org_type').value = btn.dataset.type;

            document.getElementById('org_code').value = btn.dataset.code;

            document.getElementById('org_od_code').value = btn.dataset.od_code;

            document.getElementById('org_name').value = btn.dataset.name;

            document.getElementById('org_description').value = btn.dataset.description;

            const modal = new bootstrap.Modal(document.getElementById('organizationModal'));

            modal.show();
        });
    });
}

/* =========================
   OD FIELD
========================= */

function toggleOdField() {
    const type = document.getElementById('org_type');

    const wrapper = document.getElementById('odCodeWrapper');

    if (!type || !wrapper) return;

    function update() {
        wrapper.style.display = type.value === 'OJ' ? 'block' : 'none';
    }

    update();

    type.addEventListener('change', update);
}

function initEmployeeRows() {
    document
        .querySelectorAll('.employee-row')

        .forEach((row) => {
            row.addEventListener(
                'click',

                function () {
                    const employeeId = this.dataset.id;

                    openEmployeeModal(employeeId);
                },
            );
        });
}

async function openEmployeeModal(employeeId) {
    try {
        const response = await fetch(
            APP.baseUrl + 'admin/actions/employees_get.php?id=' + employeeId,
        );

        const data = await response.json();

        if (data.status !== 'ok') {
            alert('Zaposleni nije pronađen');

            return;
        }

        const employee = data.employee;

        document.getElementById('viewEmployeePhoto').src = employee.photo
            ? APP.baseUrl + 'uploads/employees/' + employee.photo
            : APP.baseUrl + 'assets/images/default-user.png';

        // HEADER

        document.getElementById('viewEmployeeName').innerText =
            (employee.first_name ?? '') + ' ' + (employee.last_name ?? '');

        document.getElementById('viewEmployeePid').innerText = employee.personal_id ?? '-';

        document.getElementById('viewEmployeeOj').innerText = employee.unit_name ?? '-';

        document.getElementById('viewEmployeePosition').innerText = employee.position ?? '-';

        document.getElementById('viewEmployeeManagerBadge').innerText =
            employee.is_manager == 1 ? 'Rukovodilac' : 'Zaposleni';

        // PERSONAL

        document.getElementById('viewEmployeeJmbg').innerText = employee.jmbg ?? '-';

        document.getElementById('viewEmployeeBirthDate').innerText = formatDate(
            employee.birth_date,
        );

        document.getElementById('viewEmployeeAddress').innerText = employee.address ?? '-';

        document.getElementById('viewEmployeePhone').innerText = employee.phone_private ?? '-';

        document.getElementById('viewEmployeeEmail').innerText = employee.email ?? '-';

        document.getElementById('viewEmployeeBank').innerText = employee.bank_account ?? '-';

        // BUSINESS

        document.getElementById('viewEmployeeUnit').innerText = employee.unit_name ?? '-';

        document.getElementById('viewEmployeeWorkPosition').innerText = employee.position ?? '-';

        document.getElementById('viewEmployeePersonalId').innerText = employee.personal_id ?? '-';

        document.getElementById('viewEmployeeHireDate').innerText = formatDate(employee.hire_date);

        document.getElementById('viewEmployeeContractType').innerText =
            employee.contract_type ?? '-';

        document.getElementById('viewEmployeeContractEnd').innerText = formatDate(
            employee.contract_end,
        );

        document.getElementById('viewEmployeeBusinessEmail').innerText =
            employee.business_email ?? '-';

        document.getElementById('viewEmployeeBusinessPhone').innerText =
            employee.business_phone ?? '-';

        // CLOSE PREVIOUS MODAL

        const employeesModal = bootstrap.Modal.getInstance(
            document.getElementById('organizationEmployeesModal'),
        );

        if (employeesModal) {
            employeesModal.hide();
        }

        // OPEN VIEW MODAL

        const modal = new bootstrap.Modal(document.getElementById('employeeViewModal'));

        modal.show();
    } catch (error) {
        console.error(error);

        alert('Greška pri učitavanju zaposlenog');
    }
}

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
