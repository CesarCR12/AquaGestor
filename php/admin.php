<?php
include '../php/auth.php';
include '../php/auth_check.php';

if (Auth::isLoggedIn() == false || Auth::getUserRole() === 'user' || !check_login()) {
    header("Location: ../pages/index.html");
    exit();
}


$limit = 50; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$cacheDir = "../cache/";

if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true)) {
    echo('No se pudo crear el directorio de cache.');
}

$cacheFile = "{$cacheDir}users_page_{$page}_search_" . md5($search) . ".html";
$maxCacheFiles = 10;

$cacheFiles = glob("{$cacheDir}*.html");

if (count($cacheFiles) > $maxCacheFiles) {
    usort($cacheFiles, function($a, $b) {
        return filemtime($a) - filemtime($b);
    });
    
    $filesToDelete = array_slice($cacheFiles, 0, count($cacheFiles) - $maxCacheFiles);
    
    foreach ($filesToDelete as $file) {
        unlink($file);
    }
}

if (file_exists($cacheFile) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    readfile($cacheFile);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombreUsuario LIKE ? OR email LIKE ? LIMIT ? OFFSET ?");
$searchParam = "%$search%";
$stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$totalStmt = $conn->prepare("SELECT COUNT(*) AS total FROM Usuarios WHERE nombreUsuario LIKE ? OR email LIKE ?");
$totalStmt->bind_param("ss", $searchParam, $searchParam);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalUsers = $totalRow['total'];
$totalPages = ceil($totalUsers / $limit);

ob_start();
include '../pages/administracion_view.php';
$output = ob_get_clean();
file_put_contents($cacheFile, $output);

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    echo $output;
} else {
    echo $output;
}
?>
