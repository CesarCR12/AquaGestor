<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (!Auth::isLoggedIn() || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}
function doRedirect($message){
    $_SESSION['register_message'] = $message;
    $_SESSION['redirect_url'] = '../php/admin.php'; 
    header('Location: ../pages/response.html'); 
    include '../pages/user_details_view.php';
    exit();
}

$id_columna = isset($_GET['id_columna']) ? intval($_GET['id_columna']) : 0;
$nombre_tabla =  isset($_GET['nombre_tabla']) ? trim($_GET['nombre_tabla']) : '';
$nombre_columna_id =  isset($_GET['nombre_columna_id']) ? trim($_GET['nombre_columna_id']) : '';



$message = '';
if ($id_columna <= 0) {
    $message = "ID inválido.";
    doRedirect($message);
} else {
    if ($nombre_columna_id <= 0){
        $message = "ID de tabla inválido.";
        doRedirect($message);
    }
    elseif ($nombre_tabla === ''){
        $message = "Nombre de tabla invalido.";
        doRedirect($message);
    }
    else{
        $stmt = $conn->prepare("DELETE FROM ".$nombre_tabla." WHERE ".$nombre_columna_id." = ?");
        $stmt->bind_param("i", $id_columna);
        $message = 'Borrado exitoso';
        if (!$stmt->execute()) {
            $message ="Error ejecutando la consulta: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
        doRedirect($message);
    }
}


?>
