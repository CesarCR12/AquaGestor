function confirmDelete(id, role) {
    if (role === 'master') {
        alert("El usuario master no puede ser eliminado.");
        return false;
    }
    const message = "¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede revertir.";
    return confirm(message);
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const id = this.getAttribute('data-id');
            const role = this.getAttribute('data-role');
            const result = confirmDelete(id, role);
            if (!result) {
                event.preventDefault();
            }
        });
    });
});
