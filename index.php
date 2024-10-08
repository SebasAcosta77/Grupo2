<?php
<<<<<<< HEAD

/*=============================================
Mostrar errores
=============================================*/

ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log",  "D:/xampp/htdocs/apirest-dinamica/php_error_log");

/*=============================================
CORS
=============================================*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
=======
// Configuración de cabeceras CORS
header('Access-Control-Allow-Origin: *'); // Cambiar a un origen específico en producción
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
>>>>>>> parent of 4914481 (SIIIIIII)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('content-type: application/json; charset=utf-8');

<<<<<<< HEAD
/*=============================================
Requerimientos
=============================================*/

require_once "controllers/routes.controller.php";

$index = new RoutesController();
$index -> index();
=======
// Manejo de solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// Incluir controladores
require_once "controllers/routers.controllers.php";

// Inicializar el controlador de rutas
$index = new RoutersController();
$index->index();
>>>>>>> parent of 4914481 (SIIIIIII)
