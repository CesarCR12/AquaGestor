<?php
include '../php/auth.php';

if (Auth::isLoggedIn()) {
    Auth::logout();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>
<body>
    <div id="navbar-placeholder"></div>

    <div class="center-container">
        <div class="w-100 text-center" style="max-width: 400px;">
            <h1 class="mb-4">Iniciar Sesión</h1>
            <form id="loginForm" action="../php/login_user.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electrónico" autocomplete="email" required>
                    <label for="email">Correo Electrónico</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" autocomplete="current-password" required>
                    <label for="password">Contraseña</label>
                </div>
                <input type="hidden" name="action" value="login">
                <button ripple type="submit" class="btn btn-primary w-100 mt-3">Iniciar Sesión</button>
            </form>

            <div id="error-message" class="alert alert-danger mt-3 d-none"></div>        
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/login.js"></script>
    <script src="../js/rippleEffect.js"></script>

</body>
</html>
