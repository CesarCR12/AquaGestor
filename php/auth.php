<?php
session_start();
include '../php/db_connection.php';

class Auth {
    public static function login($email, $password) {
        global $conn;
        if (!$conn) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'database_error']);
            exit();
        }
        
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['contrasena'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['idUsuario'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_contrasena'] = $user['contrasena'];
                $_SESSION['user_photo'] = $user['fotoPerfil'];
                echo json_encode(['status' => 'success']);
                exit();
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'invalid_password']);
                exit();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'user_not_found']);
            exit();
        }
    }

    public static function logout() {
        $cacheDir = "../cache/";
        $cacheFiles = glob("{$cacheDir}*.html");
        foreach ($cacheFiles as $file) {
            unlink($file);
        }
        session_destroy();
        header("Location: ../pages/index.html");
        exit();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
    }

    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    public static function getUserEmail() {
        return $_SESSION['user_email'] ?? null;
    }
    public static function getUserContrasena() {
        return $_SESSION['user_contrasena'] ?? null;
    }
    public static function getUserFotoPerfil() {
        return $_SESSION['user_photo'] ?? null;
    }
}


?>
