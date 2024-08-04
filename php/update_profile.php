<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function doRedirect($message, $redirectUrl = '../pages/index.html') {
    $_SESSION['register_message'] = $message;
    $_SESSION['redirect_url'] = $redirectUrl;
    header('Location: ../pages/response.html');
    exit();
}

$idUsuario = Auth::getUserId();
$nombreUsuario = isset($_POST['nombreUsuario']) ? trim($_POST['nombreUsuario']) : '';
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

try {
    $usuario = get_4_Atributes($conn, $idUsuario);
    if (!$usuario) {
        doRedirect('Usuario no encontrado.');
    }

    $updateFields = [];
    $params = [];

    if (isset($_POST['updateNombreUsuario'])) {
        $validationResult = validateInput($nombreUsuario, $usuario['email'], $contrasena);
        if ($validationResult !== true) {
            doRedirect($validationResult);
        }

        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombreUsuario = ? AND idUsuario != ?");
        $stmt->execute([$nombreUsuario, $idUsuario]);
        if ($stmt->rowCount() > 0) {
            doRedirect("Error: El nombre de usuario ya está en uso.");
        }

        $updateFields[] = "nombreUsuario = ?";
        $params[] = $nombreUsuario;
    }

    if (isset($_POST['updateContrasena']) && !empty($contrasena)) {
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
        $updateFields[] = "contrasena = ?";
        $params[] = $contrasenaHash;
    }

    if (isset($_POST['updateFotoPerfil'])) {
        $fotoPerfil = handleFileUpload('fotoPerfil');
        if ($fotoPerfil !== null) {
            $updateFields[] = "fotoPerfil = ?";
            $params[] = $fotoPerfil;
        }
    }

    if (!empty($updateFields)) {
        $sql = "UPDATE Usuarios SET " . implode(", ", $updateFields) . " WHERE idUsuario = ?";
        $params[] = $idUsuario;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    header('Location: ../pages/perfil.php');
    exit();

} catch (Exception $e) {
    doRedirect('Error inesperado: ' . $e->getMessage());
}

function handleFileUpload($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES[$fileInputName]['type'], $allowedTypes)) {
            return 'Formato de archivo no permitido.';
        }
        $filePath = '../uploads/' . basename($_FILES[$fileInputName]['name']);
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filePath)) {
            return 'Error al subir la imagen.';
        }
        return $filePath;
    }
    return null;
}

function validateInput($nombreUsuario, $email, $password) {
    if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 50) {
        return "El nombre de usuario debe tener entre 3 y 50 caracteres.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El correo electrónico no es válido.";
    }
    if (!empty($password) && (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password))) {
        return "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.";
    }
    return true;
}
?>
