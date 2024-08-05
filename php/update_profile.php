<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}
$idUsuario = Auth::getUserId();

$usuario = get_3_Atributes_contrasena($conn, $idUsuario); 
if (!$usuario) {
    sendJsonResponse('error', 'Usuario no encontrado.', []);
}

$nombreUsuario = isset($_POST['nombreUsuario']) ? trim($_POST['nombreUsuario']) : '';
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

function sendJsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
    echo json_encode($response);
    exit();
}

function handleFileUpload($fileInputName, $currentFilePath = null) {
    $defaultImagePath = '../images/default-profile.png';
    
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES[$fileInputName]['type'], $allowedTypes)) {
            sendJsonResponse('error', 'Formato de archivo no permitido.');
        }

        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            sendJsonResponse('error', 'No se pudo crear el directorio de subida.');
        }

        $id  = Auth::getUserId();
        $uploadEspecificDir = '../uploads/user_'.$id.'_profile_img/';
        if (!is_dir($uploadEspecificDir) && !mkdir($uploadEspecificDir, 0755, true)) {
            sendJsonResponse('error', 'No se pudo crear el directorio de subida especifico del usuario.');
        }

        $filePath = $uploadEspecificDir . basename($_FILES[$fileInputName]['name']);
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filePath)) {
            sendJsonResponse('error', 'Error al subir la imagen.');
        }

        if ($currentFilePath && file_exists($currentFilePath) && $currentFilePath !== $defaultImagePath) {
            unlink($currentFilePath);
        }
        
        return $filePath;
    }

    return $currentFilePath && file_exists($currentFilePath) ? $currentFilePath : $defaultImagePath;
}

function validateInput($nombreUsuario, $email, $password) {
    if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 50) {
        sendJsonResponse('error', "El nombre de usuario debe tener entre 3 y 50 caracteres.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse('error', "El correo electrónico no es válido.");
    }
    if (!empty($password) && (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password))) {
        sendJsonResponse('error', "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.");
    }
    return true;
}

function validatePasswor($password) {
    if (!empty($password) && (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password))) {
        sendJsonResponse('error', "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.");
    }
    return true;
}

$updateFields = [];
$params = [];

// Check and update username
if (isset($_POST['updateNombreUsuario']) && $nombreUsuario !== $usuario['nombreUsuario']) {
    if ($contrasena !== ''){
        $validationResult = validateInput($nombreUsuario, $usuario['email'], $contrasena);
        if ($validationResult !== true) {
            sendJsonResponse('error', $validationResult);
        }
    }

    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombreUsuario = ? AND idUsuario != ?");
    $stmt->bind_param('si', $nombreUsuario, $idUsuario);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        sendJsonResponse('error', "Error: El nombre de usuario ya está en uso.");
    }

    $updateFields[] = "nombreUsuario = ?";
    $params[] = $nombreUsuario;
}

// Check and update password
if (isset($_POST['updateContrasena']) && !empty($contrasena) && $contrasena !== $usuario['contrasena']) {
    if (password_verify($contrasena, $usuario['contrasena'])) {
        sendJsonResponse('error', 'La contraseña no puede ser la misma.');
    } else {
        $validationResult = validatePasswor($contrasena);
        if ($validationResult !== true) {
            sendJsonResponse('error', $validationResult);
        }
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
        $updateFields[] = "contrasena = ?";
        $params[] = $contrasenaHash;
    }
}

// Check and update profile picture
if (isset($_POST['updateFotoPerfil'])) {
    $fotoPerfil = handleFileUpload('fotoPerfil', $usuario['fotoPerfil']);
    if ($fotoPerfil === false) {
        sendJsonResponse('error', 'Error al subir la imagen.');
    } else {
        $updateFields[] = "fotoPerfil = ?";
        $params[] = $fotoPerfil;
    }
}

if (!empty($updateFields)) {
    $sql = "UPDATE Usuarios SET " . implode(", ", $updateFields) . " WHERE idUsuario = ?";
    $params[] = $idUsuario;

    $types = str_repeat('s', count($params) - 1) . 'i';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        sendJsonResponse('success', 'Datos actualizados correctamente');
    } else {
        sendJsonResponse('error', 'Error al actualizar los datos');
    }
} else {
    sendJsonResponse('success', 'No se realizaron cambios');
}
?>
