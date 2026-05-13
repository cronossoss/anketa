document.addEventListener(
    'DOMContentLoaded',
    () => {

        const categorySelect =
            document.querySelector(
                'select[name="category_id"]'
            );

        const dynamicContainer =
            document.getElementById(
                'dynamic-attributes'
            );

        if (!categorySelect) {
            return;
        }

        categorySelect.addEventListener(
            'change',
            async () => {

                const categoryId =
                    categorySelect.value;

                dynamicContainer.innerHTML = '';

                if (!categoryId) {
                    return;
                }

                try {

                    const response =
                        await fetch(
                            `../api/get_attributes.php?category_id=${categoryId}`
                        );

                    const attributes =
                        await response.json();

                    attributes.forEach(
                        attribute => {

                            const wrapper =
                                document.createElement(
                                    'div'
                                );

                            wrapper.className =
                                'mb-3';

                            const label =
                                document.createElement(
                                    'label'
                                );

                            label.className =
                                'form-label';

                            label.textContent =
                                attribute.name;

                            wrapper.appendChild(
                                label
                            );

                            let field;

                            switch (
                                attribute.field_type
                            ) {

                                case 'textarea':

                                    field =
                                        document.createElement(
                                            'textarea'
                                        );

                                    field.rows = 3;

                                    break;

                                case 'select':

                                    field =
                                        document.createElement(
                                            'select'
                                        );

                                    field.innerHTML =
                                        '<option value="">Izaberi</option>';

                                    if (
                                        attribute.options
                                    ) {

                                        attribute.options
                                            .split('\n')
                                            .forEach(
                                                option => {

                                                    const opt =
                                                        document.createElement(
                                                            'option'
                                                        );

                                                    opt.value =
                                                        option.trim();

                                                    opt.textContent =
                                                        option.trim();

                                                    field.appendChild(
                                                        opt
                                                    );
                                                }
                                            );
                                    }

                                    break;

                                case 'number':

                                    field =
                                        document.createElement(
                                            'input'
                                        );

                                    field.type =
                                        'number';

                                    break;

                                case 'date':

                                    field =
                                        document.createElement(
                                            'input'
                                        );

                                    field.type =
                                        'date';

                                    break;

                                case 'checkbox':

                                    field =
                                        document.createElement(
                                            'input'
                                        );

                                    field.type =
                                        'checkbox';

                                    field.value = 1;

                                    break;

                                default:

                                    field =
                                        document.createElement(
                                            'input'
                                        );

                                    field.type =
                                        'text';
                            }

                            field.name =
                                `attributes[${attribute.id}]`;

                            field.classList.add(
                                'form-control'
                            );

                            if (
                                attribute.is_required == 1
                            ) {

                                field.required = true;
                            }

                            wrapper.appendChild(
                                field
                            );

                            dynamicContainer.appendChild(
                                wrapper
                            );
                        }
                    );

                } catch (error) {

                    console.error(error);
                }
            }
        );
    }
);