document.addEventListener('DOMContentLoaded', function () {
    setupCheckboxToggle('updateFotoPerfil', 'fotoPerfil');
    setupCheckboxToggle('updateNombreUsuario', 'nombreUsuario');
    setupCheckboxToggle('updateContrasena', 'contrasena');
});

function setupCheckboxToggle(checkboxId, inputId) {
    const checkbox = document.getElementById(checkboxId);
    const inputField = document.getElementById(inputId);
    if (checkbox && inputField) {
        checkbox.addEventListener('change', function () {
            inputField.disabled = !this.checked;
        });

        inputField.disabled = !checkbox.checked;
    }
}

