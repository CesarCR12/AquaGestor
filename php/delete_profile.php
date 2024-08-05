<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

$idUsuario = Auth::getUserId();

function deleteUser($conn, $id) {
    backupUserData($conn, $id);
    $stmt = $conn->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function fetchUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function backupUserData($conn, $id) {
    $userData = [];

    $userData['user'] = fetchUserById($conn, $id);

    $stmt = $conn->prepare("SELECT * FROM RegistroConsumoAgua WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userData['consumo'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM Reportes WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userData['reportes'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $backupDir = "../backup";

    if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
        echo ( 'No se pudo crear el directorio de respaldo.');
        exit();
    }

    $jsonFileName = "../backup/user_{$id}.json";
    file_put_contents($jsonFileName, json_encode($userData));
}

if (deleteUser($conn, $idUsuario)) {
    Auth::logout();
    header("Location: ../pages/index.html?message=Cuenta eliminada exitosamente.");
} else {
    echo "Error al eliminar la cuenta.";
}
?>
