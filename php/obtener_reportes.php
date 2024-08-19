<?php
include '../php/auth.php';
include '../php/auth_check.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

function obtenerReportes($conn, $id) {
    $sql = "SELECT * FROM reportes WHERE idUsuario = ? ORDER BY fechaReporte DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportes = [];
        while ($row = $result->fetch_assoc()) {
            $reportes[] = $row;
        }

        $stmt->close();
    } else {
        return ['status' => 'error', 'message' => 'Error al preparar la consulta'];
    }

    $conn->close();
    return ['status' => 'success', 'data' => $reportes];
}

$response = [];
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = Auth::getUserId();
    $response = obtenerReportes($conn, $id);
} else {
    $response['status'] = 'error';
    $response['message'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
?>

