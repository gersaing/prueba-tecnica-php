<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$config = require __DIR__ . '/../src/Config.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Empleado.php';
require_once __DIR__ . '/../src/controllers/EmpleadoController.php';

$db = new Database($config);
$model = new Empleado($db);
$empleadoController = new EmpleadoController($db);


$action = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'create':
        $empleadoController->create();
        break;

    case 'store':
        $empleadoController->store($_POST);
        break;
    
    case 'edit':
        $empleadoController->edit($_GET['id'] ?? null);
        break;
    case 'update':
        $empleadoController->update($_POST['id'] ?? null, $_POST);
        break;
    case 'delete':
        $empleadoController->delete($_GET['id'] ?? null);
        break;
    default:
        $empleadoController->show();
        break;
}

