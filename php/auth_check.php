<?php

function check_login() {
    return isset($_SESSION['user_id']);
}

function get_3_Atributes($conn, $idUsuario){
    $sql = "SELECT nombreUsuario, email, fotoPerfil FROM Usuarios WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    return $usuario;
}

function get_3_Atributes_contrasena($conn, $idUsuario){
    $sql = "SELECT nombreUsuario, email, fotoPerfil, contrasena FROM Usuarios WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    return $usuario;
}


function get_4_Atributes($conn, $idUsuario) {
    $sql = "SELECT rol, email, nombreUsuario, fotoPerfil FROM Usuarios WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    return $usuario;
}
?>
