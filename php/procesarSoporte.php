<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $asunto = $data['asunto'];
    $mensaje = $data['mensaje'];
    $idUsuario = Auth::getUserId(); 

    $stmt = $conn->prepare("INSERT INTO Soporte (idUsuario, mensaje, asunto) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $idUsuario, $mensaje, $asunto);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Tu solicitud de soporte ha sido enviada con éxito.']);
    } else {
        echo json_encode(['message' => 'Error al enviar la solicitud de soporte.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['message' => 'Datos inválidos.']);
}
