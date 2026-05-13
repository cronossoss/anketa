document.addEventListener(
    'DOMContentLoaded',
    () => {

        document
            .querySelectorAll('.edit-type-btn')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        document.getElementById(
                            'edit_type_id'
                        ).value =
                            button.dataset.id;

                        document.getElementById(
                            'edit_type_name'
                        ).value =
                            button.dataset.name;

                        document.getElementById(
                            'edit_type_code'
                        ).value =
                            button.dataset.code;

                        const modal =
                            new bootstrap.Modal(
                                document.getElementById(
                                    'editTypeModal'
                                )
                            );

                        modal.show();
                    }
                );
            });
    });