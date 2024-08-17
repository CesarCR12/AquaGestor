<?php
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "AquaGestor";

function handleDatabaseError($message) {
    $encodedMessage = urlencode($message);
    header("Location: ../components/error_database.php?error=" . $encodedMessage);
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }

    if (!$conn->set_charset("utf8")) {
        throw new Exception("Error al establecer el conjunto de caracteres: " . $conn->error);
    }

    if (!$conn->query("USE $dbname")) {
        throw new Exception("Base de datos no encontrada: " . $conn->error);
    }

    $result = $conn->query("SHOW FULL TABLES");

    if (!$result) {
        throw new Exception("Error al obtener las tablas: " . $conn->error);
    }

    $tables = $result->fetch_all(MYSQLI_ASSOC);

    $baseTables = array_filter($tables, function($row) use ($dbname) {
        $row = array_change_key_case($row, CASE_LOWER);
        return $row['table_type'] === 'BASE TABLE';
    });

    $baseTableNames = array_map(function($row) use ($dbname) {
        $row = array_change_key_case($row, CASE_LOWER);
        return $row['tables_in_' . strtolower($dbname)];
    }, $baseTables);

    $tableCount = count($baseTableNames);

    $expectedTables = [
        'usuarios',
        'alertas',
        'educacion',
        'registroconsumoagua',
        'reportes',
        'recomendaciones'
    ];

    if ($tableCount !== count($expectedTables)) {
        throw new Exception("Número de tablas en la base de datos no coincide con el esperado(".count($expectedTables)."). Se encontraron: " . $tableCount);
    }

    $missingTables = array_diff($expectedTables, $baseTableNames);

    if (!empty($missingTables)) {
        foreach ($missingTables as $missingTable) {
            throw new Exception("Falta la tabla esperada: $missingTable");
        }
    }

    $query = "SELECT COUNT(*) AS master_count FROM Usuarios WHERE rol = 'master'";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error al consultar los usuarios: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    $masterCount = $row['master_count'];

    if ($masterCount < 1) {
        throw new Exception("No hay usuarios con rol 'master' en la tabla Usuarios.");
    }

} catch (Exception $e) {
    handleDatabaseError($e->getMessage());
}


?>

   
   