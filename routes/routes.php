<?php

require_once "models/connection.php";
require_once "controllers/routers.controllers.php";
require_once "controllers/get.controller.php";

$routesArray = array_filter(explode("/", $_SERVER['REQUEST_URI']));

// Validamos si se hace una petición a la API
if (count($routesArray) < 2) {
    echo json_encode(["status" => 404, "results" => "Not Found"], http_response_code(404));
    return;
}

$table = explode("?", $routesArray[1])[0]; // Obtener el nombre de la tabla
$secret_key = Connection::apikey(); // Obtener la clave secreta
$headers = apache_request_headers(); // Obtener los encabezados

// Función para manejar las solicitudes
function handleRequest($method, $table) {
    switch ($method) {
        case "GET":
            include "Services/get.php";
            break;
        case "POST":
            include "Services/post.php";
            break;
        case "PUT":
            include "Services/put.php";
            break;
        case "DELETE":
            include "Services/delete.php";
            break;
        default:
            echo json_encode(["status" => 405, "results" => "Method Not Allowed"], http_response_code(405));
            break;
    }
}

// Validamos el acceso público o privado
if (in_array($table, Connection::publicAccess())) {
    $response = new GetController();
    $response->getData($table, "*", null, null, null, null);

    handleRequest($_SERVER['REQUEST_METHOD'], $table);
} else {
    if (!isset($headers['Authorization']) || $headers['Authorization'] !== $secret_key) {
        echo json_encode(["status" => 401, "results" => "Unauthorized"], http_response_code(401));
        return;
    } else {
        handleRequest($_SERVER['REQUEST_METHOD'], $table);
    }
}
