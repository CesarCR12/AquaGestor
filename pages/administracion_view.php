<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaGestor - Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/styles_front_end.css">
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
                            <input type="text" class="form-control text-center" style="width: 250px; text-align: center;" name="search" placeholder="Buscar por nombre o email" value="<?php echo htmlspecialchars($search); ?>">
                            <span class="input-group-btn">
                                <button type="submit" style="min-width: 100px" class="btn btn-primary">Buscar</button>
                            </span>
                        </div>
                    </div>
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
                            <td><?php echo htmlspecialchars($row['idUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombreUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
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
                                    <div class="button-group">
                                        <a href="../php/edit_user.php?id=<?php echo $row['idUsuario']; ?>" class="btn btn-primary btn-sm edit-button">Editar Usuario</a>
                                        <a href="../php/delete_user.php?id=<?php echo $row['idUsuario']; ?>" class="btn btn-danger btn-sm delete-button" data-id="<?php echo $row['idUsuario']; ?>" data-role="<?php echo $row['rol']; ?>">Eliminar Usuario</a>
                                        <a href="../pages/user_details_view.php?id=<?php echo $row['idUsuario']; ?>" class="btn btn-info btn-sm details-button">Detalles de Usuario</a>
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
                    <li class="page-item"><a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>">Primera</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Anterior</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Siguiente</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>">Última</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <a href="../pages/view_deleted_users.php" style=" display: flex; flex-direction: column; margin: 0 auto; text-align: center; width: 290px;" class="btn btn-warning mb-3">Ver Usuarios Eliminados</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/loadNavbar.js"></script>
    <script src="../js/deleteUser.js"></script>
    <script src="../js/administration.js"></script>
</body>

</html>
