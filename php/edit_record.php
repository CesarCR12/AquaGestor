<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

$id_columna = isset($_GET['id_columna']) ? intval($_GET['id_columna']) : 0;
$nombre_tabla = isset($_GET['nombre_tabla']) ? trim($_GET['nombre_tabla']) : '';
$nombre_columna_id = isset($_GET['nombre_columna_id']) ? trim($_GET['nombre_columna_id']) : '';

if ($id_columna <= 0 || empty($nombre_columna_id) || empty($nombre_tabla)) {
    echo json_encode(['error' => 'Parámetros inválidos.']);
    exit();
}

$nombre_tabla = $conn->real_escape_string($nombre_tabla);
$nombre_columna_id = $conn->real_escape_string($nombre_columna_id);

$query = "SELECT * FROM `$nombre_tabla` WHERE `$nombre_columna_id` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_columna);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Error ejecutando la consulta: ' . $stmt->error]);
    exit();
}

$results = $stmt->get_result();
$data = [];

if ($results->num_rows > 0) {
    $row = $results->fetch_assoc();
    foreach ($row as $column => $value) {
        if (stripos($column, 'accion') !== false || stripos($column, 'estado') !== false) {
            $data['columnaCambiable'] = $column;
            break;
        }
    }
    $data['record'] = $row;
} else {
    echo json_encode(['error' => 'No se encontraron registros']);
    exit();
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>
