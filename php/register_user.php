<?php
include '../php/auth.php';

function validateInput($nombreUsuario, $email, $password) {
    if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 50 || preg_match('/[@.]/', $nombreUsuario)) {
        return "El nombre de usuario debe tener entre 3 y 50 caracteres y no debe de contener (@ y .).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El correo electrónico no es válido.";
    }

    if (strlen($password) < 8 
        || !preg_match('/[A-Z]/', $password) 
        || !preg_match('/[a-z]/', $password) 
        || !preg_match('/[0-9]/', $password) 
        || !preg_match('/[\W]/', $password)) {
        return "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.";
    }

    return true;
}

function registerUser($nombreUsuario, $email, $password) {
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $defaultProfileImage = '../images/default-profile.png';

    // Verificación del usuario master
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE (nombreUsuario = ? OR email = ?) AND rol = 'master'");
    $stmt->bind_param("ss", $nombreUsuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Error: No puedes registrarte con este nombre de usuario o correo electrónico porque ya está registrado.";
    }

    // Verificación del usuario o email en uso
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombreUsuario = ? OR email = ?");
    $stmt->bind_param("ss", $nombreUsuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['nombreUsuario'] === $nombreUsuario) {
            return "Error: El nombre de usuario ya esta en uso.";
        }
        if ($user['email'] === $email) {
            return "Error: El correo electrónico ya esta en uso.";
        }
    }

    // Registro del usuario
    $rol = 'user';
    $stmt = $conn->prepare("INSERT INTO Usuarios (nombreUsuario, email, contrasena, rol, fotoPerfil) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombreUsuario, $email, $hashedPassword, $rol, $defaultProfileImage);

    if ($stmt->execute()) {
        return true;
    } else {
        return "Error: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['nombreUsuario']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $validationResult = validateInput($nombreUsuario, $email, $password);
    if ($validationResult !== true) {
        echo json_encode([
            'success' => false,
            'message' => $validationResult
        ]);
        exit();
    }

    $result = registerUser($nombreUsuario, $email, $password);
    if ($result !== true) {
        echo json_encode([
            'success' => false,
            'message' => $result
        ]);
        exit();
    }

    $response = Auth::login($email, $password);
    echo json_encode([
        'success' => true,
        'message' => 'Registro exitoso'
    ]);
    exit();
}

?>