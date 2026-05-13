document.addEventListener(
    'DOMContentLoaded',
    () => {

        document
            .querySelectorAll('.edit-category-btn')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        document.getElementById(
                            'edit_category_id'
                        ).value =
                            button.dataset.id;

                        document.getElementById(
                            'edit_category_name'
                        ).value =
                            button.dataset.name;

                        document.getElementById(
                            'edit_category_code'
                        ).value =
                            button.dataset.code;

                        document.getElementById(
                            'edit_category_type'
                        ).value =
                            button.dataset.typeId;

                        const modal =
                            new bootstrap.Modal(
                                document.getElementById(
                                    'editCategoryModal'
                                )
                            );

                        modal.show();
                    }
                );
            });
    }
);