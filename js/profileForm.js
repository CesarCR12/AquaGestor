document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('updateFotoPerfil').addEventListener('change', function() {
        document.getElementById('fotoPerfil').disabled = !this.checked;
    });

    document.getElementById('updateNombreUsuario').addEventListener('change', function() {
        document.getElementById('nombreUsuario').disabled = !this.checked;
    });

    document.getElementById('updateContrasena').addEventListener('change', function() {
        document.getElementById('contrasena').disabled = !this.checked;
    });
});
