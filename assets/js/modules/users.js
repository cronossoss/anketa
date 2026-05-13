document.addEventListener('DOMContentLoaded', () => {
    const selects = document.querySelectorAll('.user-role-select');

    if (!selects.length) {
        return;
    }

    selects.forEach((select) => {
        select.addEventListener('change', async function () {
            const userId = this.dataset.id;

            const role = this.value;

            const row = this.closest('tr');

            const badge = row.querySelector('.role-badge');

            try {
                const response = await fetch(
                    APP.baseUrl + 'admin/actions/users_update_role.php',

                    {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                        },

                        body: JSON.stringify({
                            user_id: userId,
                            role: role,
                            csrf: APP.csrfToken,
                        }),
                    },
                );

                const result = await response.text();

                if (result !== 'OK') {
                    return;
                }

                badge.className = 'badge role-badge';

                switch (role) {
                    case 'admin':
                        badge.classList.add('bg-danger');

                        badge.innerText = 'Admin';

                        break;

                    case 'it':
                        badge.classList.add('bg-dark');

                        badge.innerText = 'IT';

                        break;

                    case 'hr':
                        badge.classList.add('bg-info');

                        badge.innerText = 'HR';

                        break;

                    case 'manager':
                        badge.classList.add('bg-warning', 'text-dark');

                        badge.innerText = 'Manager';

                        break;

                    default:
                        badge.classList.add('bg-secondary');

                        badge.innerText = 'User';
                }
            } catch (error) {
                alert('Greška konekcije');
            }
        });
    });
});
