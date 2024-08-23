<?php
include('../php/auth.php');

$isDatabaseAvailable = $conn !== false;
$isLoggedIn = Auth::isLoggedIn();
$isAdmin = $isDatabaseAvailable && $isLoggedIn && Auth::getUserRole() == 'admin';
$isMaster = $isDatabaseAvailable && $isLoggedIn && Auth::getUserRole() == 'master';
?>
<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand logo" href="#">Aqua Gestor</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="../pages/index.html" data-bs-toggle="tooltip" title="Inicio">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <?php if ($isDatabaseAvailable && !$isLoggedIn) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/ayuda_soporte.html" data-bs-toggle="tooltip" title="Ayuda y Soporte">
                        <i class="fas fa-question-circle"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
            <?php if ($isDatabaseAvailable && $isLoggedIn) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle show" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true" title="Usuario" style="color:white;">
                        <i class="fas fa-user"></i>
                        <span style="color:white;">Usuario</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="../pages/perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
                        <a class="dropdown-item" href="../pages/dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a class="dropdown-item" href="../pages/reportes.html" data-bs-toggle="tooltip" title="Reportes">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reportes</span>
                        </a>
                        <a class="dropdown-item" href="../pages/alertas.html" data-bs-toggle="tooltip" title="Configuración de Alertas">
                            <i class="fas fa-cogs"></i>
                            <span>Alertas</span>
                        </a>
                        <a class="dropdown-item" href="../pages/registro_consumo.html" data-bs-toggle="tooltip" title="Registro de Consumo">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Consumo</span>
                            <i class="fa-solid fa-x"></i>
                        </a>
                        <?php if ($isAdmin || $isMaster) : ?>
                            <a class="dropdown-item" href="../php/admin.php"><i class="fas fa-users-cog"></i> Editar Usuarios</a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="../pages/ayuda_soporte.html" data-bs-toggle="tooltip" title="Ayuda y Soporte">
                            <i class="fas fa-question-circle"></i>
                            <span>Ayuda y Soporte</span>
                        </a>
                        <form method="POST" action="../php/login_user.php" class="px-4 py-2">
                            <input type="hidden" name="action" value="logout">
                            <button ripple type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
                        </form>
                    </div>
                </li>
            <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/login.php" data-bs-toggle="tooltip" title="Inicio de Sesión">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/registro.php" data-bs-toggle="tooltip" title="Registro de Usuario">
                        <i class="fas fa-user-plus"></i>
                        <span>Registro</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>