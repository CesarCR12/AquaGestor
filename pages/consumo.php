<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'tu_servidor';
    $db = 'tu_base_de_datos';
    $user = 'tu_usuario';
    $password = 'tu_contraseña';
    $charset = 'utf8mb4';

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $password, $options);

        $idUsuario = $_POST['idUsuario'];
        $cantidad = $_POST['cantidad'];
        $ubicacion = $_POST['ubicacion'];

        $stmt = $pdo->prepare('INSERT INTO Registro_Consumo (idUsuario, Cantidad, ubicacion, fechaConsumo) VALUES (:idUsuario, :cantidad, :ubicacion, NOW())');

        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_STR);
        $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount()) {
            echo 'El registro de consumo de agua ha sido guardado con éxito.';
        } else {
            echo 'No se pudo guardar el registro de consumo de agua.';
        }
    } catch (PDOException $e) {
        error_log('Error de conexión: ' . $e->getMessage());
        echo 'Error al conectar con la base de datos.';
    }
} else {
    echo 'Este script solo puede ser accedido mediante una solicitud POST.';
}
?>