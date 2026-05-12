document.addEventListener(
    'DOMContentLoaded',
    () => {

        initEmployeeSearch();

        initEditButtons();

        initCreateButton();

        initDatepickers();

        const hasAccount =
            document.getElementById(
                'has_account'
            );

        if (hasAccount) {

            hasAccount.addEventListener(
                'change',
                toggleSystemRole
            );
        }
    }
);

/* =========================
   DATEPICKER
========================= */

function initDatepickers() {

    document
        .querySelectorAll('.datepicker')
        .forEach(el => {

            flatpickr(el, {

                locale: "sr",

                dateFormat: "Y-m-d",

                altInput: true,

                altFormat: "d.m.Y",

                allowInput: true
            });
        });
}

/* =========================
   HELPERS
========================= */

function setValue(id, value) {

    const element =
        document.getElementById(id);

    if (!element) return;

    element.value =
        value ?? '';
}

function setDate(id, value) {

    const element =
        document.getElementById(id);

    if (!element) return;

    const formatted =
        value
            ? value.split(' ')[0]
            : '';

    if (element._flatpickr) {

        element._flatpickr.setDate(
            formatted,
            true
        );

    } else {

        element.value =
            formatted;
    }
}

function toggleSystemRole() {

    const hasAccount =
        document.getElementById(
            'has_account'
        );

    const wrapper =
        document.getElementById(
            'system_role_wrapper'
        );

    if (!hasAccount || !wrapper) return;

    if (hasAccount.checked) {

        wrapper.style.display =
            'block';

    } else {

        wrapper.style.display =
            'none';

        document.getElementById(
            'system_role'
        ).value = '';
    }
}

/* =========================
   SEARCH
========================= */

function initEmployeeSearch() {

    const search =
        document.getElementById(
            'employeeSearch'
        );

    if (!search) return;

    search.addEventListener(
        'keyup',
        function () {

            const value =
                this.value.toLowerCase();

            document
                .querySelectorAll(
                    '.employee-row'
                )
                .forEach(row => {

                    row.style.display =
                        row.innerText
                            .toLowerCase()
                            .includes(value)
                                ? ''
                                : 'none';
                });
        }
    );
}

/* =========================
   CREATE
========================= */

function initCreateButton() {

    const btn =
        document.getElementById(
            'addEmployeeBtn'
        );

    if (!btn) return;

    btn.addEventListener(
        'click',
        () => {

            resetEmployeeForm();

            document.getElementById(
                'employeeModalTitle'
            ).innerText =
                'Dodavanje zaposlenog';

            document.getElementById(
                'employeeForm'
            ).action =
                APP.baseUrl +
                'admin/actions/employees_create.php';

            document.getElementById(
                'employeePhotoPreview'
            ).src =
                APP.baseUrl +
                'assets/images/default-user.png';
        }
    );
}

/* =========================
   EDIT
========================= */

function initEditButtons() {

    document
        .querySelectorAll(
            '.edit-employee-btn, .view-employee-btn'
        )
        .forEach(btn => {

            btn.addEventListener(
                'click',
                async () => {

                    resetEmployeeForm();

                    const id =
                        btn.dataset.id;

                    const response =
                        await fetch(
                            APP.baseUrl +
                            'admin/actions/employees_get.php?id=' +
                            id
                        );

                    const data =
                        await response.json();

                    if (
                        data.status !== 'ok'
                    ) {

                        alert('Greška');

                        return;
                    }

                    const e =
                        data.employee;

                    console.log(e);

                    document.getElementById(
                        'employeeDisplayName'
                    ).innerText =
                        e.first_name + ' ' + e.last_name;

                    document.getElementById(
                        'employeeDisplayPid'
                    ).innerText =
                        e.personal_id ?? '-';

                    document.getElementById(
                        'employeeDisplayPosition'
                    ).innerText =
                        e.position ?? '-';

                    document.getElementById(
                        'employeeDisplayOj'
                    ).innerText =
                        '(' +
                        (e.unit_code ?? '-') +
                        ') ' +
                        (e.unit_name ?? '-');

                    document.getElementById(
                        'employeeModalTitle'
                    ).innerText =
                        'Podaci o zaposlenom';

                    document.getElementById(
                        'employeeForm'
                    ).action =
                        APP.baseUrl +
                        'admin/actions/employees_update.php';

                    setValue(
                        'employee_id',
                        e.id
                    );

                    setValue(
                        'first_name',
                        e.first_name
                    );

                    setValue(
                        'last_name',
                        e.last_name
                    );

                    setValue(
                        'position',
                        e.position
                    );

                    setValue(
                        'email',
                        e.email
                    );

                    setValue(
                        'personal_id',
                        e.personal_id
                    );

                    setValue(
                        'jmbg',
                        e.jmbg
                    );

                    setValue(
                        'address',
                        e.address
                    );

                    setValue(
                        'phone_private',
                        e.phone_private
                    );

                    setValue(
                        'bank_account',
                        e.bank_account
                    );

                    setValue(
                        'business_email',
                        e.business_email
                    );

                    setValue(
                        'business_phone',
                        e.business_phone
                    );

                    setValue(
                        'contract_type',
                        e.contract_type
                    );

                    setValue(
                        'organizational_unit_id',
                        e.organizational_unit_id
                    );

                    setDate(
                        'birth_date',
                        e.birth_date
                    );

                    setDate(
                        'hire_date',
                        e.hire_date
                    );

                    setDate(
                        'contract_end',
                        e.contract_end
                    );

                    document.getElementById(
                        'is_manager'
                    ).checked =
                        e.is_manager === 1;

                    document.getElementById(
                        'has_account'
                    ).checked =
                        e.has_account === 1;

                    document.getElementById(
                        'system_role'
                    ).value =
                        e.system_role || '';

                    toggleSystemRole();

                    const preview =
                        document.getElementById(
                            'employeePhotoPreview'
                        );

                    if (e.photo) {

                        preview.src =
                            APP.baseUrl +
                            'uploads/employees/' +
                            e.photo;

                    } else {

                        preview.src =
                            APP.baseUrl +
                            'assets/images/default-user.png';
                    }

                    const modalElement =
                        document.getElementById(
                            'employeeModal'
                        );

                    let modal =
                        bootstrap.Modal.getInstance(
                            modalElement
                        );

                    if (!modal) {

                        modal =
                            new bootstrap.Modal(
                                modalElement
                            );
                    }

                    modal.show();
                }
            );
        });
}

/* =========================
   RESET FORM
========================= */

function resetEmployeeForm() {

    document.getElementById(
        'employeeForm'
    ).reset();

    document.getElementById(
        'employee_id'
    ).value = '';
}
