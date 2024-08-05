<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>

<body>
    <div id="navbar-placeholder"></div>
    <div class="center-container-2" style="height: 70vh;">
        <div class="w-400" style="max-width: 400px;">
            <h1 class="mb-2 mt-5 text-center">Editar Usuario</h1>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-floating mb-2 mt-5">
                    <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" value="<?php echo htmlspecialchars($user['nombreUsuario']); ?>" autocomplete="username" required>
                    <label for="nombreUsuario">Nombre de Usuario</label>
                    <p><small class="form-text text-danger mt-2" style="font-size: 0.8rem;">⚠️ Cuidado: Cambiar el nombre de usuario puede afectar la sesión actual. Asegúrate de ingresar un nombre válido.</small></p>
                </div>
                <div class="form-floating mb-2 mt-5">
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" autocomplete="email" required>
                    <label for="email">Correo Electrónico</label>
                    <p><small class="form-text text-danger" style="font-size: 0.8rem;">⚠️ Cambiar el correo puede afectar tu sesión. Usa un correo válido.</small></p>
                </div>
                <div class="form-floating mb-2 mt-5">
                    <select class="form-control" id="rol" name="rol" required>
                        <option value="1" <?php echo $user['rol'] == 1 ? 'selected' : ''; ?>>Usuario</option>
                        <option value="2" <?php echo $user['rol'] == 2 ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <label for="rol">Rol</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
                <a href="../php/admin.php" class="btn btn-secondary mt-3">Cancelar</a>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
</body>

</html>