<?php
include '../php/auth.php';
include '../php/auth_check.php';
header('Content-Type: application/json');

if (Auth::isLoggedIn() == false || !check_login()) {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$idUsuario = isset($_GET['id']) ? intval($_GET['id']) : 0;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'TODOS';
$search = isset($_GET['search']) ? intval($_GET['search']) : 0;

if ($idUsuario === 0 || $idUsuario === null) {
    echo json_encode(["error" => "ID de usuario no válido"]);
    exit();
}

$query = "CALL BuscarRegistros(?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(["error" => "Error preparando la consulta: " . $conn->error]);
    exit();
}

$stmt->bind_param("sii", $filter, $idUsuario, $search);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Error ejecutando la consulta: " . $stmt->error]);
    exit();
}

$result = $stmt->get_result();

if ($result === false) {
    echo json_encode(["error" => "Error en la consulta SQL: " . $conn->error]);
    exit();
}

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

$stmt->close();
$conn->close();

if (empty($results)) {
    echo json_encode(["error" => "No se encontraron resultados para la búsqueda."]);
} else {
    echo json_encode(["success" => $results]);
}
?>
