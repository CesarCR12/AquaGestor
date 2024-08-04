<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function fetchUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function isMasterUser($user) {
    return $user['rol'] === 'master';
}

function hasAssociatedRecords($conn, $id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM RegistroConsumoAgua WHERE idUsuario = ? UNION ALL SELECT COUNT(*) FROM Reportes WHERE idUsuario = ?");
    $stmt->bind_param("ii", $id, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if ($row['total'] > 0) {
            return true;
        }
    }
    return false;
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

    $jsonFileName = "../backup/user_{$id}.json";
    file_put_contents($jsonFileName, json_encode($userData));
}

function deleteUser($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function handleDeleteUser($conn, $id) {
    $user = fetchUserById($conn, $id);

    if (isMasterUser($user)) {
        echo "El usuario master no puede ser eliminado.";
        exit();
    }

    if (hasAssociatedRecords($conn, $id)) {
        backupUserData($conn, $id);
    }

    if (deleteUser($conn, $id)) {
        if ($_SESSION['user_id'] == $id) {
            Auth::logout();
        }
        header("Location: ../php/admin.php?message=Usuario eliminado exitosamente.");
    } else {
        echo "Error al eliminar el usuario.";
    }
}


if (isset($_GET['id'])) {
    handleDeleteUser($conn, $_GET['id']);
}
?>
