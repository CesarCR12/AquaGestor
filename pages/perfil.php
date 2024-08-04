<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}
$idUsuario = Auth::getUserId();

$usuario = get_3_Atributes($conn, $idUsuario);


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="center-container-3">
        <div class="w-100 text-center" style="max-width: 400px;">
            <h1 class="mb-4 mt-4">Perfil de Usuario</h1>
            <form class="mb-3 mt-4" id="sign_in_form" action="../php/update_profile.php" method="POST" enctype="multipart/form-data">
                <?php if (!empty($usuario['fotoPerfil'])) : ?>
                    <img src="<?php echo htmlspecialchars($usuario['fotoPerfil']); ?>" alt="Foto de Perfil" class="img-thumbnail mt-2 mb-3 mt-4" style="width: 150px;">
                <?php endif; ?>

                <div class="row align-items-center mb-3">
                    <div class="col-10">
                        <div class="form-floating">
                            <input type="file" class="form-control" id="fotoPerfil" name="fotoPerfil" disabled>
                            <label for="fotoPerfil">Actualizar Foto de Perfil</label>
                        </div>
                    </div>
                    <div class="col-2 text-end">
                        <input type="checkbox" id="updateFotoPerfil" name="updateFotoPerfil">
                    </div>
                </div>

                <div class="row align-items-center mb-3">
                    <div class="col-10">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" value="<?php echo htmlspecialchars($usuario['nombreUsuario']); ?>" autocomplete="username" required>
                            <label for="nombreUsuario">Nombre de Usuario</label>
                        </div>
                    </div>
                    <div class="col-2 text-end">
                        <input type="checkbox" id="updateNombreUsuario" name="updateNombreUsuario" checked>
                    </div>
                </div>

                <div class="form-floating mb-3" style="width: 330px;">
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled readonly required>
                    <label for="email">Correo Electrónico</label>
                </div>

                <div class="row align-items-center mb-3">
                    <div class="col-10">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="contrasena" name="contrasena" disabled autocomplete="current-password">
                            <label for="contrasena">Cambiar Contraseña</label>
                            <small class="form-text text-muted">Desmarca con el check si no deseas cambiar la contraseña.</small>
                        </div>
                    </div>
                    <div class="col-2 text-end">
                        <input type="checkbox" id="updateContrasena" name="updateContrasena" style="margin-top: -20px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Actualizar Perfil</button>
            </form>
            <form action="../php/delete_profile.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta?');">
                <button type="submit" class="btn btn-danger w-100 mt-3 delete-button"  data-id="<?php echo Auth::getUserId(); ?>" data-role="<?php echo Auth::getUserRole(); ?>">Eliminar Cuenta</button>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/validateForm.js"></script>
    <script src="../js/deleteUser.js"></script>
    <script src="../js/profileForm.js"></script>
</body>

</html>