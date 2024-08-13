<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function insertarReporte($conn, $id, $mensajeReporte, $fechaReporte, $horaReporte) {
    
    $horaReporteObj = new DateTime($horaReporte);
    $horaReporteFormateada = $horaReporteObj->format('H:i'); 

    $fechaFinal = $fechaReporte . ' ' . $horaReporteFormateada;

    $fechaActual = new DateTime();
    $fechaReporteObj = new DateTime($fechaReporte);

    $message_response = [
        'status' => 'error',
        'message' => ''
    ];

    if ($fechaReporteObj > $fechaActual) {
        $message_response['message'] = "La fecha del reporte no puede ser mayor a la fecha actual.";
        return $message_response;
    }

    $sql = "INSERT INTO reportes (idUsuario, mensajeReporte, fechaReporte) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iss", $id, $mensajeReporte, $fechaFinal);

        if ($stmt->execute()) {
            $message_response['status'] = 'success';
            $message_response['message'] = "Reporte enviado exitosamente.";
        } else {
            $message_response['message'] = "Error al ejecutar la consulta: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message_response['message'] = "Error al preparar la consulta: " . $conn->error;
    }

    $conn->close();
    return $message_response;
}

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = Auth::getUserId();
    $mensajeReporte = $_POST['mensajeReporte'];
    $fechaReporte = $_POST['fechaReporte'];
    $horaReporte = $_POST['horaReporte'];
    
    $response = insertarReporte($conn, $id, $mensajeReporte, $fechaReporte, $horaReporte);
} else {
    $response['status'] = 'error';
    $response['message'] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>
