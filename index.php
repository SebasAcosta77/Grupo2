<?php
<<<<<<< HEAD
// Configuración de cabeceras CORS
header('Access-Control-Allow-Origin: *'); // Cambiar a un origen específico en producción
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=utf-8'); 

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
=======
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/dashboard/');
	exit;
?>
Something is wrong with the XAMPP installation :-(
>>>>>>> parent of 9376664 (02-10-24-1pm)
