<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

// Inicializa variables para mensajes de éxito o error
$mensajeExito = '';
$mensajeError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $mensajeReporte = $_POST['mensajeReporte'];
    $fechaReporte = $_POST['fechaReporte'];

    // Verifica si el usuario está autenticado
    if (!Auth::isLoggedIn()) {
        $mensajeError = "Usuario no autenticado.";
    } else {
        // Obtiene el ID del usuario desde la sesión
        $idUsuario = Auth::getUserId();

        // Establece la conexión con la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Consulta SQL para insertar el reporte
        $sql = "INSERT INTO reportes (idUsuario, mensajeReporte, fechaReporte, estado)
                VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Vincula los parámetros
            $estado = 'pendiente'; // Estado por defecto
            $stmt->bind_param("isss", $idUsuario, $mensajeReporte, $fechaReporte, $estado);

            // Ejecuta la consulta
            if ($stmt->execute()) {
                $mensajeExito = "Reporte enviado exitosamente.";
            } else {
                $mensajeError = "Error al ejecutar la consulta: " . $stmt->error;
            }

            // Cierra la declaración
            $stmt->close();
        } else {
            // Si la preparación falló, muestra el error
            $mensajeError = "Error al preparar la consulta: " . $conn->error;
        }

        // Cierra la conexión
        $conn->close();
    }

    // Guarda los mensajes en sessionStorage
    session_start();
    $_SESSION['mensajeExito'] = $mensajeExito;
    $_SESSION['mensajeError'] = $mensajeError;

    // Redirige a la página HTML
    header("Location: ../pages/reportes.html");
    exit();
}
?>







