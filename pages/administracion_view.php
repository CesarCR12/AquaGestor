<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
    <style>
        /* Referencia para dropdowns anidados de : https://es.stackoverflow.com/questions/159744/c%C3%B3mo-a%C3%B1adir-un-dropdownmenu-dentro-de-otro-bootstrap */
        .dropdown-menu > li {
            position: relative;
            -webkit-user-select: none; 
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
            cursor: pointer;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .dropdown-menu .sub-menu {
            left: 30%;
            position: absolute;
            top: 0;
            display: none;
            margin-top: -1px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left-color: #fff;
            box-shadow: none;
        }

        .right-caret:after, .left-caret:after {
            content: "";
            border-bottom: 5px solid transparent;
            border-top: 5px solid transparent;
            display: inline-block;
            height: 0;
            vertical-align: middle;
            width: 0;
            margin-left: 5px;
        }

        .right-caret:after {
            border-left: 5px solid #ffaf46;
        }

        .left-caret:after {
            border-right: 5px solid #ffaf46;
        }
        .shadow_box_{
            box-sizing: border-box;
            box-shadow: 0px 4px 16px 0px rgba(0, 0, 0, 0.5);

        }
    </style>
</head>

<body>
    <div id="navbar-placeholder"></div>
    <div class="container mb-3">
        <h1>Administración de Usuarios</h1>
        <form method="GET" class="mb-3 d-flex justify-content-center" style="width: 100%; max-width: 600vh;">
            <div class="row g-2 align-items-center">
                <div class="col-md">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control text-center shadow_box_"
                                style="width: 250px; text-align: center;" name="search"
                                placeholder="Buscar por nombre o email"
                                value="<?php echo htmlspecialchars($search); ?>">
                            <span class="input-group-btn">
                                <button ripple type="submit" style="min-width: 100px;"
                                    class="btn btn-primary">Buscar</button>
                            </span>
                        </div>
                    </div>
                    <a href="../pages/view_deleted_users.php"
                    style=" display: flex; flex-direction: column; margin: 0 auto; text-align: center; width: 290px;"
                    class="btn btn-warning mb-3 mt-3 shadow_box_">Ver Usuarios Eliminados</a>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($row['idUsuario']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nombreUsuario']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['email']); ?>
                        </td>
                        <td>
                            <?php
                                if ($row['rol'] === 'master') {
                                    echo htmlspecialchars('Master');
                                } elseif ($row['rol'] === 'admin') {
                                    echo htmlspecialchars('Admin');
                                } else {
                                    echo htmlspecialchars('Usuario');
                                }
                                ?>
                        </td>
                        <td>
                            <?php if ($row['rol'] !== 'master') : ?>
                                <div class="dropdown">
                                    <button ripple class="btn btn-dark btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $row['idUsuario']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ver opciones del usuario  <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="../php/edit_user.php?id=<?php echo $row['idUsuario']; ?>">Editar Usuario</a></li>
                                        <li><a class="dropdown-item" href="../php/delete_user.php?id=<?php echo $row['idUsuario']; ?>" data-id="<?php echo $row['idUsuario']; ?>" data-role="<?php echo $row['rol']; ?>">Eliminar Usuario</a></li>
                                        <li><a class="dropdown-item" href="../pages/user_details_view.php?id=<?php echo $row['idUsuario']; ?>">Detalles de Usuario</a></li>
                                        <li>
                                            <button ripple class="trigger right-caret btn btn-secondary btn-sm" style="transform: translateX(5%);" type="button" id="dropdownMenuButton_2_<?php echo $row['idUsuario']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Descargar Detalles
                                            </button>
                                            <ul class="dropdown-menu sub-menu">
                                                <li><a class="dropdown-item" href="../pages/soporte_download.php?id=<?php echo $row['idUsuario']; ?>">Descargar Detalles Soporte</a></li>
                                                <li><a class="dropdown-item" href="../pages/reportes_download.php?id=<?php echo $row['idUsuario']; ?>">Descargar Detalles Reportes</a></li>
                                                <li><a class="dropdown-item" href="../pages/recomendaciones_download.php?id=<?php echo $row['idUsuario']; ?>">Descargar Detalles Recomendaciones</a></li>
                                                <li><a class="dropdown-item" href="../pages/consumo_download.php?id=<?php echo $row['idUsuario']; ?>">Descargar Detalles Consumo de Agua</a></li>
                                                <li><a class="dropdown-item" href="../pages/alertas_download.php?id=<?php echo $row['idUsuario']; ?>">Descargar Detalles Alertas</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            <?php else : ?>
                                <span style="display: flex; justify-content: center; align-items: center; text-align: center; gap: 10px;">No editable</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination">
                <?php if ($page > 1) : ?>
                <li class="page-item"><a class="page-link"
                        href="?page=1&search=<?php echo urlencode($search); ?>">Primera</a></li>
                <li class="page-item"><a class="page-link"
                        href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Anterior</a></li>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++) : ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                <li class="page-item"><a class="page-link"
                        href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Siguiente</a>
                </li>
                <li class="page-item"><a class="page-link"
                        href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>">Última</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/deleteUser.js"></script>
    <script src="../js/administration.js"></script>
    <script src="../js/rippleEffect.js"></script>
</body>
</html>