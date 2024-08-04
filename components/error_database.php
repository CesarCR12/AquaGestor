<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Conexión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>
<body>
    <div class="container mt-5" id="alert_error_database">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Error de Conexión</h4>
            <?php
            if (isset($_GET['error'])) {
                $error_message = htmlspecialchars($_GET['error']);
            } else {
                $error_message = "Lo sentimos, no podemos conectar con la base de datos en este momento.";
            }
            ?>
            <p><?php echo $error_message; ?></p>
            <hr>
            <p class="mb-0">Por favor, vuelve a intentar más tarde o contacta con el administrador.</p>
            <button id="hideAlertButton" class="btn btn-primary mt-3">Volver al Inicio</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('hideAlertButton').addEventListener('click', function() {
            document.getElementById('alert_error_database').style.display = 'none';
            window.location.href = '../pages/index.html'; 
        });
    </script>
</body>
</html>
