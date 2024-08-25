<?php
include '../php/auth.php';
include '../php/auth_check.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn() || Auth::getUserRole() === 'user' || !check_login()) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fechaConsumo = $_POST['fechaConsumo']; 
    $cantidad = floatval($_POST['cantidad']);
    $ubicacion = trim($_POST['ubicacion']);

    $idUsuario = Auth::getUserId();
    if ($idUsuario === null) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no encontrado.']);
        exit();
    }

    $query = "INSERT INTO RegistroConsumoAgua (idUsuario, fechaConsumo, cantidad, ubicacion) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("isis", $idUsuario, $fechaConsumo, $cantidad, $ubicacion);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Registro de consumo de agua realizado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar el consumo de agua.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta.']);
    }

    $conn->close();
}
?>


