<?php
include '../php/auth.php';
include '../php/auth_check.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

function obtenerConsumos($conn, $idUsuario) {
    $sql = "SELECT * FROM RegistroConsumoAgua WHERE idUsuario = ? ORDER BY fechaConsumo DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $consumos = [];
        while ($row = $result->fetch_assoc()) {
            $consumos[] = $row;
        }

        $stmt->close();
    } else {
        return ['status' => 'error', 'message' => 'Error al preparar la consulta'];
    }

    $conn->close();
    return ['status' => 'success', 'data' => $consumos];
}

$response = [];
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $idUsuario = Auth::getUserId();
    if ($idUsuario === null) {
        $response = ['status' => 'error', 'message' => 'ID de usuario no encontrado.'];
    } else {
        $response = obtenerConsumos($conn, $idUsuario);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
?>



