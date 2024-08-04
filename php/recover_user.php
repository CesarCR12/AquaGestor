<?php
include '../php/auth.php';
include '../php/auth_check.php';

function doRedirect($message){
    $_SESSION['register_message'] = $message;
    $_SESSION['redirect_url'] = '../php/admin.php'; 
    header('Location: ../pages/response.html'); 
    include '../pages/edit_user_view.php';
    exit();
}

if (!Auth::isLoggedIn() || Auth::getUserRole() !== 'admin' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

if (isset($_POST['restore_user'])) {
    $jsonFileName = "../backup/user_{$_POST['user_id']}.json";
    if (file_exists($jsonFileName)) {
        $userData = json_decode(file_get_contents($jsonFileName), true);

        $stmt = $conn->prepare("INSERT INTO Usuarios (nombreUsuario, email, contrasena, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $userData['user']['nombreUsuario'], $userData['user']['email'], $userData['user']['contrasena'], $userData['user']['rol']);
        $stmt->execute();
        $newUserId = $stmt->insert_id;

        foreach ($userData['consumo'] as $consumo) {
            $stmt = $conn->prepare("INSERT INTO RegistroConsumoAgua (idUsuario, fecha, Cantidad, ubicacion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $newUserId, $consumo['fecha'], $consumo['Cantidad'], $consumo['ubicacion']);
            $stmt->execute();
        }

        foreach ($userData['reportes'] as $reporte) {
            $stmt = $conn->prepare("INSERT INTO Reportes (idUsuario, mensajeReporte, fechaReporte, estado) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $newUserId, $reporte['mensajeReporte'], $reporte['fechaReporte'], $reporte['estado']);
            $stmt->execute();
        }

        $message = "Usuario restaurado exitosamente.";
        doRedirect($message);
    } else {
        $message = "Archivo de respaldo no encontrado.";
        doRedirect($message);
    }
}
?>
