<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function fetchUserById($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}

function isMasterUser($user) {
    return $user['rol'] === 'master';
}

function hasAssociatedRecords($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM RegistroConsumoAgua WHERE idUsuario = ? UNION ALL SELECT COUNT(*) FROM Reportes WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($row['total'] > 0) {
                return true;
            }
        }
        return false;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function deleteUserProfileDirectory($userId) {
    $uploadEspecificDir = '../uploads/user_' . $userId . '_profile_img/';
    
    if (is_dir($uploadEspecificDir)) {
        try {
            if (deleteDirectory($uploadEspecificDir)) {
                return 'El directorio y su contenido fueron eliminados exitosamente.';
            } else {
                throw new Exception('No se pudo eliminar el directorio de subida específico del usuario.');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $e->getMessage();
        }
    } else {
        return 'El directorio no existe.';
    }
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $dir . '/' . $file;
        if (is_dir($filePath)) {
            deleteDirectory($filePath); 
        } else {
            if (!unlink($filePath)) {
                throw new Exception("No se pudo eliminar el archivo: $filePath");
            }
        }
    }

    if (!rmdir($dir)) {
        throw new Exception("No se pudo eliminar el directorio: $dir");
    }

    return true;
}

function backupUserData($conn, $id) {
    try {
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
            throw new Exception('No se pudo crear el directorio de respaldo.');
        }
        
        $jsonFileName = "../backup/user_{$id}.json";
        if (file_put_contents($jsonFileName, json_encode($userData)) === false) {
            throw new Exception("No se pudo crear el archivo de respaldo: $jsonFileName");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "Error al hacer el respaldo de datos.";
        exit();
    }
}

function deleteUser($conn, $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
        }
        return true;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function handleDeleteUser($conn, $id) {
    try {
        $conn->begin_transaction(); 

        $user = fetchUserById($conn, $id);
        if (!$user) {
            throw new Exception("Usuario no encontrado.");
        }

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
            $conn->commit(); 
            header("Location: ../php/admin.php?message=Usuario eliminado exitosamente.");
            exit();
        } else {
            throw new Exception("Error al eliminar el usuario.");
        }
    } catch (Exception $e) {
        $conn->rollback(); 
        error_log($e->getMessage());
        echo "Ha ocurrido un error: " . $e->getMessage();
    }
}

if (isset($_GET['id'])) {
    handleDeleteUser($conn, $_GET['id']);
} else {
    echo "ID de usuario no proporcionado.";
}
?>
