<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}

$id_columna = isset($_POST['id_columna']) ? intval($_POST['id_columna']) : 0;
$nombre_tabla = isset($_POST['nombre_tabla']) ? trim($_POST['nombre_tabla']) : '';
$nombre_columna_id = isset($_POST['nombre_columna_id']) ? trim($_POST['nombre_columna_id']) : '';
$nuevo_valor = isset($_POST['nuevo_valor']) ? trim($_POST['nuevo_valor']) : '';

if ($id_columna <= 0 || empty($nombre_columna_id) || empty($nombre_tabla) || empty($nuevo_valor)) {
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
$row = $results->fetch_assoc();
$columnaCambiable = '';

if ($results->num_rows > 0) {
    foreach ($row as $column => $value) {
        if (stripos($column, 'accion') !== false || stripos($column, 'estado') !== false) {
            $columnaCambiable = $column;
            break;
        }
    }
} else {
    echo json_encode(['error' => 'No se encontraron registros']);
    exit();
}

if ($nuevo_valor === $row[$columnaCambiable]) {
    echo json_encode(['error' => 'Debe cambiar el valor del campo.']);
    exit();
}

$update_query = "UPDATE `$nombre_tabla` SET `$columnaCambiable` = ? WHERE `$nombre_columna_id` = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $nuevo_valor, $id_columna);

if (!$update_stmt->execute()) {
    echo json_encode(['error' => 'Error actualizando el registro: ' . $update_stmt->error]);
} else {
    echo json_encode(['success' => 'Registro actualizado exitosamente.']);
}

$update_stmt->close();
$conn->close();
?>
