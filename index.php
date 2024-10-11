<?php

/*=============================================
Mostrar errores
=============================================*/

// Configura la visualización de errores para el desarrollo
ini_set('display_errors', 1); // Mostrar errores en pantalla
ini_set("log_errors", 1); // Registrar errores en un archivo
ini_set("error_log", "D:/xampp/htdocs/apirest-dinamica/php_error_log"); // Ruta del archivo de registro de errores

/*=============================================
CORS
=============================================*/

// Permitir el acceso desde cualquier origen (ajusta según tus necesidades de seguridad)
header('Access-Control-Allow-Origin: *');
// Especificar los encabezados permitidos
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// Especificar los métodos HTTP permitidos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
// Establecer el tipo de contenido de la respuesta
header('Content-Type: application/json; charset=utf-8');

/*=============================================
Requerimientos
=============================================*/

// Incluir el controlador de rutas
require_once "controllers/routes.controller.php";

// Crear una instancia del controlador de rutas y ejecutar el método index
$index = new RoutesController();
$index->index();
