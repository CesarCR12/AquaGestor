document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch('../php/login_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '../pages/index.html';
            } else {
                errorMessage.textContent = getErrorMessage(data.message);
                errorMessage.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = 'Ocurrió un error inesperado. Intenta de nuevo.';
            errorMessage.classList.remove('d-none');
        });

    });

    function getErrorMessage(errorCode) {
        switch (errorCode) {
            case 'invalid_password':
                return 'La contraseña es incorrecta. Intenta de nuevo.';
            case 'user_not_found':
                return 'El usuario no se encontró. Verifica tu email.';
            case 'database_error':
                return 'Error en la conexión a la base de datos. Intenta más tarde.';
            default:
                return 'Ocurrió un error desconocido. Intenta de nuevo.';
        }
    }

    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    const error = getQueryParam('error');
    if (error) {
        const message = getErrorMessage(error);
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');
    }
});
