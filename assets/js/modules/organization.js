document.addEventListener(
    'DOMContentLoaded',
    () => {

        initCreateButton();

        initEditButtons();

        toggleOdField();
    }
);


/* =========================
   CREATE
========================= */

function initCreateButton() {

    const btn =
        document.getElementById(
            'addOrganizationBtn'
        );

    if (!btn) return;

    btn.addEventListener(
        'click',
        () => {

            document.getElementById(
                'organizationForm'
            ).reset();

            document.getElementById(
                    'organizationModalTitle'
                ).innerText =
                'Dodavanje organizacije';

            document.getElementById(
                    'organizationForm'
                ).action =
                APP.baseUrl +
                'admin/actions/organization_create.php';

            const modal =
                new bootstrap.Modal(
                    document.getElementById(
                        'organizationModal'
                    )
                );

            modal.show();
        }
    );
}


/* =========================
   EDIT
========================= */

function initEditButtons() {

    document
        .querySelectorAll(
            '.edit-unit-btn'
        )
        .forEach(btn => {

            btn.addEventListener(
                'click',
                () => {

                    document.getElementById(
                            'organizationModalTitle'
                        ).innerText =
                        'Izmena organizacije';

                    document.getElementById(
                            'organizationForm'
                        ).action =
                        APP.baseUrl +
                        'admin/actions/organization_update.php';

                    document.getElementById(
                            'org_id'
                        ).value =
                        btn.dataset.id;

                    document.getElementById(
                            'org_type'
                        ).value =
                        btn.dataset.type;

                    document.getElementById(
                            'org_code'
                        ).value =
                        btn.dataset.code;

                    document.getElementById(
                            'org_od_code'
                        ).value =
                        btn.dataset.od_code;

                    document.getElementById(
                            'org_name'
                        ).value =
                        btn.dataset.name;

                    document.getElementById(
                            'org_description'
                        ).value =
                        btn.dataset.description;

                    const modal =
                        new bootstrap.Modal(
                            document.getElementById(
                                'organizationModal'
                            )
                        );

                    modal.show();
                }
            );
        });
}


/* =========================
   OD FIELD
========================= */

function toggleOdField() {

    const type =
        document.getElementById(
            'org_type'
        );

    const wrapper =
        document.getElementById(
            'odCodeWrapper'
        );

    if (!type || !wrapper) return;

    function update() {

        wrapper.style.display =
            type.value === 'OJ'
            ? 'block'
            : 'none';
    }

    update();

    type.addEventListener(
        'change',
        update
    );
}