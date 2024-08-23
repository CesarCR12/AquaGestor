<?php
    include '../php/auth.php';
    include '../php/auth_check.php';

    if (Auth::isLoggedIn() == false || Auth::getUserRole() === 'user' || !check_login()) {
        header("Location: ../pages/index.html");
        exit();
    }

    $idUsuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $query = "SELECT * FROM RegistroConsumoAgua WHERE idUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idUsuario);
    if (!$stmt->execute()) {
        echo ('Error al enviar la solicitud de consumo de agua.');
    }
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    ob_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Registro de Consumo de Agua</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link type="text/css" media="screen" rel="stylesheet" href="../css/styles_front_end.css">
    <style>
        #img_logo_aG {
            max-width: 200px;
            height: auto;
            position: absolute;
            bottom: 0;
            right: 0;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container mb-3">
        <div>
            <h1 class="text-center mt-3 mb-4">Registro de Consumo de Agua</h1>
        </div>
        <div class="container mb-3">
            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Consumo</th>
                            <th>Cantidad</th>
                            <th>Ubicación</th>
                            <th>Fecha Consumo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['idConsumo']); ?></td>
                                <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                                <td><?php echo htmlspecialchars($row['fechaConsumo']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron registros de consumo de agua para este usuario.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <img class="img-thumbnail rounded" id="img_logo_aG" src="http://localhost/AquaGestor/images/image_logo_aquaGestor.png" alt="Logo">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
<?php
    $html = ob_get_clean();
    require_once '../library/dompdf/autoload.inc.php';
    use Dompdf\Dompdf;
    $dompdf = new Dompdf();
    
    $options = $dompdf->getOptions();
    $options->set('isRemoteEnabled', true);
    $dompdf->setOptions($options);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("reportes_usuario_". $idUsuario. ".pdf", array("Attachment" => false));
?>
