<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Conexion.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'auth/login':
        (new AuthController())->login();
        break;
    case 'auth/register':
        (new AuthController())->register();
        break;
    default: 
        require_once APP_ROOT . '/Views/home.php';
        break;
}
?>