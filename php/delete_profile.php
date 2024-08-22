<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

$idUsuario = Auth::getUserId();


function deleteUser($conn, $id) {
    $conn->begin_transaction();
    try {
        $response = deleteUserProfileDirectory($id);
        if (hasAssociatedRecords($conn, $id) != false) {
            $stmt = $conn->prepare("DELETE FROM RegistroConsumoAgua WHERE idUsuario = ?");

            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
            }
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
            }
            backupUserData($conn, $id);
        }
            
        $stmt = $conn->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
        }
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        return false;
    }
}

function hasAssociatedAlerts($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM Alertas WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['total'] > 0;
        }
        return false;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function hasAssociatedRecords($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM RegistroConsumoAgua WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['total'] > 0;
        }
        return false;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function fetchUserById($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta SQL: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
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
    $userData = [];

    $userData['user'] = fetchUserById($conn, $id);

    $stmt = $conn->prepare("SELECT * FROM RegistroConsumoAgua WHERE idUsuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userData['consumo'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (hasAssociatedAlerts($conn, $id) != false) {
        $stmt = $conn->prepare("SELECT * FROM Alertas WHERE idUsuario =?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $userData['alertas'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    $backupDir = "../backup";

    if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
        throw new Exception('No se pudo crear el directorio de respaldo.');
    }

    $jsonFileName = "../backup/user_{$id}.json";
    if (file_put_contents($jsonFileName, json_encode($userData)) === false) {
        throw new Exception("No se pudo crear el archivo de respaldo: $jsonFileName");
    }
}

try {
    if (deleteUser($conn, $idUsuario) != false) {
        Auth::logout();
        header("Location: ../pages/index.html?message=Cuenta eliminada exitosamente.");
        exit();
    } else {
        throw new Exception("Error al eliminar la cuenta.");
    }
} catch (Exception $e) {
    $conn->rollback(); 
    error_log($e->getMessage()); 
    echo "Ha ocurrido un error: " . $e->getMessage();
}


?>
