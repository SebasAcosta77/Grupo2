<?php

require_once "models/connection.php";
require_once "controllers/get.controller.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

/*=============================================
Cuando no se hace ninguna petición a la API
=============================================*/
if (count($routesArray) == 0) {
    $json = array(
        'status' => 404,
        'results' => 'Not Found'
    );
    echo json_encode($json, http_response_code($json["status"]));
    return;
}

/*=============================================
Cuando sí se hace una petición a la API
=============================================*/
if (count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])) {
    $table = explode("?", $routesArray[1])[0];

    /*=============================================
    Validar llave secreta
    =============================================*/
    $headers = getallheaders();
    $isAuthorized = isset($headers["Authorization"]) && $headers["Authorization"] == Connection::apikey();
    
    /*=============================================
    Acceso a las tablas públicas
    =============================================*/
    if (in_array($table, Connection::publicAccess())) {
        // Se permite el acceso tanto si hay autorización como si no
        $response = new GetController();
        $response->getData($table, "*", null, null, null, null);
        return;
    }

    /*=============================================
    Acceso a tablas no públicas solo con autorización
    =============================================*/
    if ($isAuthorized) {
        // Si tiene autorización, puede acceder a cualquier tabla
        $response = new GetController();
        $response->getData($table, "*", null, null, null, null);
        return;
    } else {
        // Si no tiene autorización y la tabla no es pública, se deniega el acceso
        $json = array(
            'status' => 403,
            "results" => "You are not authorized to make this request"
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }
}

/*=============================================
Peticiones GET
=============================================*/
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    include "services/get.php";
}

/*=============================================
Peticiones POST
=============================================*/
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "services/post.php";
}

/*=============================================
Peticiones PUT
=============================================*/
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    include "services/put.php";
}

/*=============================================
Peticiones DELETE
=============================================*/
if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    include "services/delete.php";
}
