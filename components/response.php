<?php
session_start();
header('Content-Type: application/json');

$response = array();
$response['message'] = isset($_SESSION['register_message']) ? $_SESSION['register_message'] : 'Ocurrió un error.';
$response['redirectUrl'] = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '../pages/index.html';

$jsonResponse = json_encode($response);
unset($_SESSION['register_message']);
unset($_SESSION['redirect_url']);

echo $jsonResponse;
?>
