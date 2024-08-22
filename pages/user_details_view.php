<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Usuario - AquaGestor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .table-container {
            max-height: 60vh;
            overflow-y: auto;
            overflow-x: auto;
            margin-top: 2rem;
        }
        .table {
            width: 100%;
            margin: auto;
        }

        .table thead th {
            text-align: center;
        }

        .table-container,
        .table {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="container">
        <h1 class="mb-3 mt-4">Detalles del Usuario</h1>

        <div class="mb-5" style="width: 400px; margin: auto;">
            <form id="filter-form" class="mb-4 mt-2">
                <div class="form-floating mb-3">
                    <select class="form-select" name="filter" id="filter-select" style="max-width: 400px;">
                        <option value="TODOS" selected>Todos</option>
                        <option value="Recomendaciones">Recomendaciones</option>
                        <option value="Reportes">Reportes</option>
                        <option value="RegistroConsumoAgua">Registro de Consumo de Agua</option>
                        <option value="Alertas">Alertas</option>
                        <option value="Soporte">Soporte</option>
                    </select>
                    <label for="filter-select" class="form-label">Filtrar por</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="search-input" placeholder="Filtrar...">
                    <label for="search-input" class="form-label">Cantidad a Filtrar</label>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                </div>
            </form>
        </div>
        <div class="mb-5 text-center">

            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Filter</th>
                            <th>Detalles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="data-table-body">
                        <tr>
                            <td colspan="4" class="text-center">No se encontraron más registros para este usuario.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-3 text-center">
            <a href="../php/admin.php" class="btn btn-secondary mt-5 mb-5 text-center">Volver a la Administración de Usuarios</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/fetch_user_details.js"></script>
</body>

</html>
