async function loadEmployeeAssets(employeeId) {

    const container =
        document.getElementById(
            'viewEmployeeAssetsContainer'
        );

    if (!container) {
        return;
    }

    container.innerHTML = `
        <div class="text-muted">
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

        if (!Array.isArray(assets) || assets.length === 0) {

            container.innerHTML = `
                <div class="text-muted">
                    Nema zaduženog inventara.
                </div>
            `;

            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Inventarski broj</th>
                            <th>Kategorija</th>
                            <th>Proizvođač</th>
                            <th>Model</th>
                            <th>Zadužen</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        assets.forEach(asset => {

            html += `
                <tr>
                    <td>${asset.inventory_number ?? '-'}</td>
                    <td>${asset.category_name ?? '-'}</td>
                    <td>${asset.manufacturer ?? '-'}</td>
                    <td>${asset.model ?? '-'}</td>
                    <td>${asset.assigned_at ?? '-'}</td>
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
            <div class="text-danger">
                Greška pri učitavanju inventara.
            </div>
        `;
    }
}

async function openEmployeeModal(employeeId) {

    try {

        const response =
            await fetch(
                APP.baseUrl +
                '/admin/actions/employees_get.php?id=' +
                employeeId
            );

        const data =
            await response.json();

        if (data.status !== 'ok') {

            alert(
                'Zaposleni nije pronađen'
            );

            return;
        }

        const employee =
            data.employee;

        const photo =
            document.getElementById(
                'viewEmployeePhoto'
            );

        photo.onerror = function () {

            this.onerror = null;

            this.src =
                APP.baseUrl +
                '/assets/images/default-user.png';
        };

        photo.src = employee.photo
            ? APP.baseUrl +
              '/uploads/employees/' +
              employee.photo
            : APP.baseUrl +
              '/assets/images/default-user.png';

        document.getElementById(
            'viewEmployeeName'
        ).innerText =
            (employee.first_name ?? '') +
            ' ' +
            (employee.last_name ?? '');

        document.getElementById(
            'viewEmployeePid'
        ).innerText =
            employee.personal_id ?? '-';

        document.getElementById(
            'viewEmployeeOj'
        ).innerText =
            employee.unit_name ?? '-';

        document.getElementById(
            'viewEmployeePosition'
        ).innerText =
            employee.position ?? '-';

        document.getElementById(
            'viewEmployeeJmbg'
        ).innerText =
            employee.jmbg ?? '-';

        document.getElementById(
            'viewEmployeeBirthDate'
        ).innerText =
            formatDate(
                employee.birth_date
            );

        document.getElementById(
            'viewEmployeeAddress'
        ).innerText =
            employee.address ?? '-';

        document.getElementById(
            'viewEmployeePhone'
        ).innerText =
            employee.phone_private ?? '-';

        document.getElementById(
            'viewEmployeeEmail'
        ).innerText =
            employee.email ?? '-';

        document.getElementById(
            'viewEmployeeBank'
        ).innerText =
            employee.bank_account ?? '-';

        document.getElementById(
            'viewEmployeeUnit'
        ).innerText =
            employee.unit_name ?? '-';

        document.getElementById(
            'viewEmployeeWorkPosition'
        ).innerText =
            employee.position ?? '-';

        document.getElementById(
            'viewEmployeePersonalId'
        ).innerText =
            employee.personal_id ?? '-';

        document.getElementById(
            'viewEmployeeHireDate'
        ).innerText =
            formatDate(
                employee.hire_date
            );

        document.getElementById(
            'viewEmployeeContractType'
        ).innerText =
            employee.contract_type ?? '-';

        document.getElementById(
            'viewEmployeeContractEnd'
        ).innerText =
            formatDate(
                employee.contract_end
            );

        document.getElementById(
            'viewEmployeeBusinessEmail'
        ).innerText =
            employee.business_email ?? '-';

        document.getElementById(
            'viewEmployeeBusinessPhone'
        ).innerText =
            employee.business_phone ?? '-';

        const roleBadge =
            document.getElementById(
                'viewEmployeeRoleBadge'
            );

        roleBadge.className =
            Number(employee.is_manager) === 1
                ? 'badge bg-warning text-dark'
                : 'badge bg-secondary';

        roleBadge.innerText =
            Number(employee.is_manager) === 1
                ? 'Rukovodilac'
                : 'Zaposleni';

        const employeesModal =
            bootstrap.Modal.getInstance(
                document.getElementById(
                    'organizationEmployeesModal'
                )
            );

        if (employeesModal) {
            employeesModal.hide();
        }

        const modal =
            new bootstrap.Modal(
                document.getElementById(
                    'employeeViewModal'
                )
            );

        modal.show();

        loadEmployeeAssets(employeeId);

    } catch (error) {

        console.error(error);

        alert(
            'Greška pri učitavanju zaposlenog'
        );
    }
}