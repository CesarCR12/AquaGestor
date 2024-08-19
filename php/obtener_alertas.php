<?php
include '../php/auth.php';
include '../php/auth_check.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

function obtenerAlertas($conn, $id) {
    $sql = "SELECT Alertas.mensaje, Alertas.fechaAlerta, Usuarios.nombreUsuario 
            FROM Alertas 
            JOIN Usuarios ON Alertas.idUsuario = Usuarios.idUsuario 
            WHERE Alertas.idUsuario = ? 
            ORDER BY fechaAlerta DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $alertas = [];
        while ($row = $result->fetch_assoc()) {
            $alertas[] = $row;
        }

        $stmt->close();
    } else {
        return ['status' => 'error', 'message' => 'Error al preparar la consulta'];
    }

    $conn->close();
    return ['status' => 'success', 'data' => $alertas];
}

$response = [];
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = Auth::getUserId();
    $response = obtenerAlertas($conn, $id);
} else {
    $response['status'] = 'error';
    $response['message'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
?>