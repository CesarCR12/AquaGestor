document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sign_in_form');

    form.addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/;

        if (!passwordRegex.test(password)) {
            alert('La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.');
            event.preventDefault();
            return false;
        }
    });
});
