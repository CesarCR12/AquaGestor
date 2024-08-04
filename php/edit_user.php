<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function doRedirect($message){
    $_SESSION['register_message'] = $message;
    $_SESSION['redirect_url'] = '../php/admin.php'; 
    header('Location: ../pages/response.html'); 
    include '../pages/edit_user_view.php';
    exit();
}


function getUserById($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        doRedirect("Usuario no encontrado.");
    } else {
        return $result->fetch_assoc();
    }
}

function checkDuplicateAdmin($conn, $nombreUsuario, $email) {
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE (nombreUsuario = ? OR email = ?) AND rol = 3");
    $stmt->bind_param("ss", $nombreUsuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        doRedirect("Error: No puedes registrarte con este nombre de usuario o correo electrónico porque ya está registrado.");
    }
}

function updateUser($conn, $userId, $nombre, $email, $rol) {
    $stmt = $conn->prepare("UPDATE Usuarios SET nombreUsuario = ?, email = ?, rol = ? WHERE idUsuario = ?");
    $stmt->bind_param("sssi", $nombre, $email, $rol, $userId);

    if ($stmt->execute()) {
        header("Location: ../php/admin.php?update=success");
        exit();
    } else {
        doRedirect("Error al actualizar el usuario.");
    }
}


if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $user = getUserById($conn, $userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombreUsuario'] ?? ''; 
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';

        checkDuplicateAdmin($conn, $nombre, $email);

        if ($user['rol'] === 'master') {
            doRedirect("No se puede cambiar el rol del Master Admin.");
        } else if ($user['rol'] === '' || $user['rol'] === null || $user == null) {
            doRedirect("Ocurrio un error al ingresar los datos intentalo de nuevo.");
        } else {
            updateUser($conn, $userId, $nombre, $email, $rol);
        }
    }

    include '../pages/edit_user_view.php';
} else {
    doRedirect("ID de usuario no especificado.");
}