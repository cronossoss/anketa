document.addEventListener('DOMContentLoaded', () => {

    const modalElement =
        document.getElementById('attributeModal');

    if (!modalElement) return;

    const modal =
        new bootstrap.Modal(modalElement);

    const form =
        modalElement.querySelector('form');

    document
        .querySelectorAll('.edit-attribute-btn')
        .forEach(button => {

            button.addEventListener('click', () => {

                document.getElementById('attribute_id').value =
                    button.dataset.id;

                document.getElementById('category_id').value =
                    button.dataset.categoryId;

                document.getElementById('name').value =
                    button.dataset.name;

                document.getElementById('code').value =
                    button.dataset.code;

                document.getElementById('field_type').value =
                    button.dataset.fieldType;

                document.getElementById('options').value =
                    button.dataset.options;

                document.getElementById('sort_order').value =
                    button.dataset.sort;

                document.getElementById('is_required').checked =
                    button.dataset.required == '1';

                form.action =
                    '../actions/asset_attribute_update.php';

                modalElement
                    .querySelector('.modal-title')
                    .textContent =
                        'Izmeni atribut';

                modal.show();
            });
        });

    modalElement.addEventListener(
        'hidden.bs.modal',
        () => {

            form.reset();

            form.action =
                '../actions/asset_attribute_create.php';

            document.getElementById('attribute_id').value = '';

            modalElement
                .querySelector('.modal-title')
                .textContent =
                    'Dodaj atribut';
        }
    );
});