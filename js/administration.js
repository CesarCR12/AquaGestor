document.addEventListener('DOMContentLoaded', function() {
    function loadUsers(page, search) {
        fetch(`../php/admin.php?page=${page}&search=${encodeURIComponent(search)}&ajax=true`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            console.log('Fetched data:', data);
            const container = document.querySelector('.container');
            if (container) {
                container.innerHTML = data;
            } else {
                console.error('Container not found');
            }
        })        
        .catch(error => console.error('Error:', error));
    }

    const form = document.querySelector('form');
    const pagination = document.querySelector('.pagination');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const search = document.querySelector('input[name="search"]').value;
            loadUsers(1, search);
        });
    }

    if (pagination) {
        pagination.addEventListener('click', function(event) {
            const target = event.target.closest('a');
            if (target) {
                event.preventDefault();
                const page = new URL(target.href).searchParams.get('page');
                const search = document.querySelector('input[name="search"]').value;
                loadUsers(page, search);
            }
        });
    }

});
