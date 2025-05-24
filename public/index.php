<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/Core/Conexion.php';

$action = $_GET['action'] ?? 'home';

if ($action === 'auth/register') {
    $authController = new \App\Controllers\AuthController();
    $authController->register();
} elseif ($action === 'home') {
    include __DIR__ . '/../app/Views/home.php';
}

?>