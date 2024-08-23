<?php
include '../php/auth.php';
include '../php/auth_check.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn() || Auth::getUserRole() === null || !check_login()) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

$idUsuario = Auth::getUserId();


$more = [];
$errorMessages = [];
$consumo_data = [];
$soporte_data = [];
$alertas_data = [];

// Consulta de consumo de agua
$sql_consumo = "SELECT DATE(fechaConsumo) as fecha, SUM(cantidad) as total 
                FROM RegistroConsumoAgua 
                WHERE idUsuario = ? 
                GROUP BY DATE(fechaConsumo)";
$stmt = $conn->prepare($sql_consumo);
if (!$stmt) {
    $errorMessages[] = 'Error en la consulta de consumo de agua: ' . $conn->error . '.' ;
} else {
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultsss = $stmt->get_result();

    if ($resultsss->num_rows > 0) {
        while($row = $resultsss->fetch_assoc()) {
            $consumo_data[] = $row;
        }
    } else {
        $errorMessages[] = 'No se encontraron datos de consumo de agua.';
    }
}

// Consulta de soporte
$sql_soporte = "SELECT accion, COUNT(*) as total 
               FROM Soporte 
               WHERE idUsuario = ? 
               GROUP BY accion";
$stmt = $conn->prepare($sql_soporte);
if (!$stmt) {
    $errorMessages[] = 'Error en la consulta de soporte: ' . $conn->error . '.';
} else {
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultss = $stmt->get_result();

    if ($resultss->num_rows > 0) {
        while($row = $resultss->fetch_assoc()) {
            $soporte_data[] = $row;
        }
    } else {
        $errorMessages[] = 'No se encontraron datos de soporte.';
    }
}

// Consulta de alertas
$sql_alertas = "SELECT COUNT(*) as total_alertas 
                FROM Alertas 
                WHERE idUsuario = ?";
$stmt = $conn->prepare($sql_alertas);
if (!$stmt) {
    $errorMessages[] = 'Error en la consulta de alertas: ' . $conn->error . '.' ;
} else {
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $results = $stmt->get_result();
    if ($results->num_rows > 0) {
        while($row = $results->fetch_assoc()) {
            if ($row['total_alertas'] > 0) {
                $alertas_data[] = $row;
            } else {
                $errorMessages[] = 'No se encontraron datos de alertas.';
            }
        }
    } else {
        $errorMessages[] = 'No se encontraron datos de alertas.';
    }
}


$response = [
    'status' => !empty($errorMessages) ? 'error' : 'success',
    'message' => !empty($errorMessages) ? implode("\n", $errorMessages) : '',
    'consumo' => $consumo_data,
    'soporte' => $soporte_data,
    'alertas' => $alertas_data
];
echo json_encode($response);

$conn->close();


// function debug_to_console($data)
// {
//     $output = $data;
//     if (is_array($output))
//         $output = implode(',', $output);

//     echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
// }

?>
