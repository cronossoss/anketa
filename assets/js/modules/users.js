document.addEventListener(
    'DOMContentLoaded',
    () => {

        const toggles =
            document.querySelectorAll(
                '.role-toggle'
            );

        if (!toggles.length) {
            return;
        }

        toggles.forEach(toggle => {

            toggle.addEventListener(
                'change',
                function () {

                    const id =
                        this.dataset.id;

                    const checkbox =
                        this;

                    const badge =
                        this.closest('td')
                            .querySelector(
                                '.role-badge'
                            );

                            console.log(APP.csrfToken);

                    fetch(
                        APP.baseUrl +
                        'admin/actions/users_toggle_role.php',

                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/x-www-form-urlencoded'
                            },

                            body:
                                'id=' + id +
                                '&csrf=' +
                                encodeURIComponent(
                                    APP.csrfToken
                                )
                        }
                    )
                    .then(async res => {

                      const text = await res.text();

                      console.log(text);

                      return JSON.parse(text);
                  })

                    .then(data => {

                        if (
                            data.status === 'ok'
                        ) {

                            if (
                                data.role === 'admin'
                            ) {

                                badge.classList.remove(
                                    'bg-secondary'
                                );

                                badge.classList.add(
                                    'bg-danger'
                                );

                                badge.innerText =
                                    'Admin';

                            } else {

                                badge.classList.remove(
                                    'bg-danger'
                                );

                                badge.classList.add(
                                    'bg-secondary'
                                );

                                badge.innerText =
                                    'User';
                            }

                        } else {

                            checkbox.checked =
                                !checkbox.checked;

                            alert('Greška');
                        }
                    })

                    .catch(error => {

                        console.error(error);

                        checkbox.checked =
                            !checkbox.checked;

                        alert(
                            'Greška konekcije'
                        );
                    });
                }
            );
        });
    }
);