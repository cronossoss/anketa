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
                    APP.baseUrl +
                        '/admin/actions/organization_employees.php?unit_id=' +
                        unitId,
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
            APP.baseUrl + '/admin/actions/organization_create.php';

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
                APP.baseUrl + '/admin/actions/organization_update.php';

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