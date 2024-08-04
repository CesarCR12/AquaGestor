<?php
session_start();
include '../php/db_connection.php';

function validateInput($nombreUsuario, $email, $password) {
    if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 50) {
        return "El nombre de usuario debe tener entre 3 y 50 caracteres.";
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

    // Se verifica si el usuario ya está registrado como master
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE (nombreUsuario = ? OR email = ?) AND rol = 3");
    $stmt->bind_param("ss", $nombreUsuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Error: No puedes registrarte con este nombre de usuario o correo electrónico porque ya está registrado.";
    }

    // Se verifica si el usuario o email ya están en uso
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

    // Se registra el usuario normalmente
    $rol = 1;
    $stmt = $conn->prepare("INSERT INTO Usuarios (nombreUsuario, email, contrasena, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombreUsuario, $email, $hashedPassword, $rol);

    if ($stmt->execute()) {
        return "Usuario registrado exitosamente.";
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
        $_SESSION['register_message'] = $validationResult;
        $_SESSION['redirect_url'] = '../pages/index.html'; 
        header('Location: ../pages/response.html'); 
        exit();
    }

    $result = registerUser($nombreUsuario, $email, $password);
    $_SESSION['register_message'] = $result;
    $_SESSION['redirect_url'] = '../pages/index.html'; 
    include '../pages/response.php';
    header('Location: ../pages/response.html'); 
    exit();
}
?>
