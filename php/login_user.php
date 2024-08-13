<?php
include '../php/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $response = Auth::login($email, $password);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'logout') {
        Auth::logout();
    }
}else {
    Auth::logout();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'action_missing']);
    exit();
}
?>