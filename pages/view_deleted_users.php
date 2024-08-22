<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

$backupDir = '../backup/';


if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
    echo( 'No se pudo crear el directorio de respaldo.');
    exit();
}

$files = array_diff(scandir($backupDir), array('..', '.'));

$users = [];
foreach ($files as $file) {
    if (preg_match('/^user_(\d+)\.json$/', $file, $matches)) {
        $userId = $matches[1];
        $userData = json_decode(file_get_contents($backupDir . $file), true);
        $userData['user']['id'] = $userId;
        $users[] = $userData['user'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Usuarios Eliminados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>

<body>
    <div id="navbar-placeholder"></div>
    <div class="container mt-4">
        <h1>Usuarios Eliminados</h1>
        <div class="alert alert-info" style="text-align: center;" role="alert">
            Aquí solo se muestran los usuarios que han sido eliminados y que tenían al menos una alerta o un consumo registrado.
        </div>

        <?php if (empty($users)) : ?>
            <div class="alert alert-warning w-50" style="margin: 0 auto; text-align: center;" role="alert">
                No hay usuarios eliminados que cumplan con los criterios especificados.
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['nombreUsuario']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['rol']); ?></td>
                                <td>
                                    <form method="post" action="../php/recover_user.php">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                        <button type="submit" name="recover_user" class="btn btn-success btn-sm">Restaurar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <a href="../php/admin.php" style="height: auto;margin-left: 50%;margin-top: 1%;transform: translateX(-50%);" class="btn btn-secondary">Volver</a>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
</body>

</html>