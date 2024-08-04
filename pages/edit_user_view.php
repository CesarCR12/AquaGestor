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
    <div class="container">
        <h1>Editar Usuario</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="nombreUsuario">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" value="<?php echo htmlspecialchars($user['nombreUsuario']); ?>" autocomplete="username" required>
                <small class="form-text text-danger">⚠️ Cuidado: Cambiar el nombre de usuario puede afectar la sesión actual. Asegúrate de ingresar un nombre válido.</small>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" autocomplete="email" required>
                <small class="form-text text-danger">⚠️ Cuidado: Cambiar el correo electrónico puede afectar la sesión actual. Asegúrate de ingresar un correo válido.</small>
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="1" <?php echo $user['rol'] == 1 ? 'selected' : ''; ?>>Usuario</option>
                    <option value="2" <?php echo $user['rol'] == 2 ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="../php/admin.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
</body>
</html>
