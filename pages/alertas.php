<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Alertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
</head>
<body>
    <div id="navbar-placeholder"></div>

    <div class="container mt-4">
        <h1>Alertas</h1>
        <p>Visualiza y envía alertas relacionadas con el consumo de agua.</p>

        <div class="row">
            <div class="col-md-6 formulario-wrapper">
                <div id="mensaje-alerta" class="mt-3 w-50" style="max-width: 400px; margin-left: 5vh;"></div>
                <form id="formularioAlerta" action="../php/procesar_alerta.php" method="POST">
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="mensajeAlerta" name="mensajeAlerta" rows="3" required></textarea>
                        <label for="mensajeAlerta" class="form-label">Mensaje de la Alerta</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="date" class="form-control" id="fechaAlerta" name="fechaAlerta" required>
                        <label for="fechaAlerta" class="form-label">Fecha de la Alerta</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="time" class="form-control" id="horaAlerta" name="horaAlerta" required>
                        <label for="horaAlerta" class="form-label">Hora de la Alerta</label>
                        <label for="horaAlerta" class="hora-label" style="margin-left: 38vh;">Formato: 24H</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-50 mt-3" style="margin-left: 5vh;">Enviar Alerta</button>
                </form>
            </div>

           <div class="col-md-6">
    <h2>Alertas Recientes</h2>
    <div id="alertas-list" class="list-group">
        <?php
        // Conectar a la base de datos
        include '../php/db_connection.php'; // Cambia esto según tu archivo de conexión
        $conn = open_connection(); // Cambia según tu función de conexión

        // Consulta para obtener las alertas recientes
        $sql = "SELECT Alertas.mensaje, Alertas.fechaAlerta, Usuarios.nombreUsuario 
                FROM Alertas 
                JOIN Usuarios ON Alertas.idUsuario = Usuarios.idUsuario 
                ORDER BY fechaAlerta DESC 
                LIMIT 10"; // Puedes ajustar el límite

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Iterar a través de las alertas y mostrarlas
            while($row = $result->fetch_assoc()) {
                echo '<div class="list-group-item">';
                echo '<h5 class="mb-1">' . htmlspecialchars($row["mensaje"]) . '</h5>';
                echo '<small>Por: ' . htmlspecialchars($row["nombreUsuario"]) . ' - ' . htmlspecialchars($row["fechaAlerta"]) . '</small>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-muted">No hay alertas recientes.</p>';
        }

        $conn->close();
        ?>
    </div>
</div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/alertas.js"></script>
</body>
</html>