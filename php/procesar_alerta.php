<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

function insertarAlerta($conn, $id, $mensajeAlerta, $fechaAlerta, $horaAlerta) {

    $horalAlertaObj = new DateTime($horaAlerta);
    $horaAlertaFormateada = $horalAlertaObj->format('H:i'); 
    $fechaFinal = $fechaAlerta . ' ' . $horaAlertaFormateada;

    $fechaActual = new DateTime();
    $fechaAlertaObj = new DateTime($fechaAlerta);


    $message_response = [
        'status' => 'error',
        'message' => ''
    ];

    if ($fechaAlertaObj > $fechaActual) {
        $message_response['message'] = "La fecha del alerta no puede ser mayor a la fecha actual.";
        return $message_response;
    }

    $sql = "INSERT INTO alertas (idUsuario, mensaje, fechaAlerta) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iss", $id, $mensajeAlerta, $fechaFinal);

        if ($stmt->execute()) {
            $message_response['status'] = 'success';
            $message_response['message'] = "Alerta enviada exitosamente.";
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
    $mensajeAlerta = $_POST['mensajeAlerta'];
    $fechaAlerta = $_POST['fechaAlerta'];
    $horaAlerta = $_POST['horaAlerta'];

    $response = insertarAlerta($conn, $id, $mensajeAlerta, $fechaAlerta, $horaAlerta);
} else {
    $response['status'] = 'error';
    $response['message'] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>