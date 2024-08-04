<?php
include '../php/auth.php';
include '../php/auth_check.php';

function doRedirect($message) {
    $_SESSION['register_message'] = $message;
    $_SESSION['redirect_url'] = '../php/admin.php'; 
    header('Location: ../pages/response.html'); 
    exit();
}

function validateUserAccess() {
    if (!Auth::isLoggedIn() || Auth::getUserRole() !== 'admin' || !check_login()) {
        header("Location: ../pages/index.html");
        exit();
    }
}

function insertUserData($conn, $userData) {
    $stmt = $conn->prepare("INSERT INTO Usuarios (nombreUsuario, email, contrasena, rol) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $message = "Error en la preparación de la consulta para usuarios: " . $conn->error;
        doRedirect($message);
    }
    $stmt->bind_param("ssss", $userData['user']['nombreUsuario'], $userData['user']['email'], $userData['user']['contrasena'], $userData['user']['rol']);
    if (!$stmt->execute()) {
        $message = "Error al insertar el usuario: " . $stmt->error;
        doRedirect($message);
    }
    $newUserId = $stmt->insert_id;
    $stmt->close();
    return $newUserId;
}

function insertConsumoData($conn, $userData, $newUserId) {
    foreach ($userData['consumo'] as $consumo) {
        $stmt = $conn->prepare("INSERT INTO RegistroConsumoAgua (idUsuario, fecha, Cantidad, ubicacion) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $message = "Error en la preparación de la consulta para consumo: " . $conn->error;
            doRedirect($message);
        }
        $stmt->bind_param("isss", $newUserId, $consumo['fecha'], $consumo['Cantidad'], $consumo['ubicacion']);
        if (!$stmt->execute()) {
            $message = "Error al insertar el consumo: " . $stmt->error;
            doRedirect($message);
        }
        $stmt->close();
    }
}

function insertReportesData($conn, $userData, $newUserId) {
    foreach ($userData['reportes'] as $reporte) {
        $stmt = $conn->prepare("INSERT INTO Reportes (idUsuario, mensajeReporte, fechaReporte, estado) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $message = "Error en la preparación de la consulta para reportes: " . $conn->error;
            doRedirect($message);
        }
        $stmt->bind_param("issi", $newUserId, $reporte['mensajeReporte'], $reporte['fechaReporte'], $reporte['estado']);
        if (!$stmt->execute()) {
            $message = "Error al insertar el reporte: " . $stmt->error;
            doRedirect($message);
        }
        $stmt->close();
    }
}

function restoreUserFromFile($conn, $fileName) {
    if (file_exists($fileName)) {
        $userData = json_decode(file_get_contents($fileName), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = "Error en el archivo JSON.";
            doRedirect($message);
        }

        $newUserId = insertUserData($conn, $userData);
        insertConsumoData($conn, $userData, $newUserId);
        insertReportesData($conn, $userData, $newUserId);
        $conn->close();

        if (!unlink($fileName)) {
            $message = "Error al eliminar el archivo de respaldo.";
            doRedirect($message);
        }

        $message = "Usuario restaurado exitosamente.";
        doRedirect($message);
    } else {
        $message = "Archivo de respaldo no encontrado.";
        doRedirect($message);
    }
}

validateUserAccess();

if (isset($_POST['recover_user'])) {
    $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($userId === false) {
        $message = "ID de usuario inválido.";
        doRedirect($message);
    }

    $jsonFileName = "../backup/user_{$userId}.json";
    restoreUserFromFile($conn, $jsonFileName);
}
?>
