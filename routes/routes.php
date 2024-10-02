<?php

require_once "models/connection.php";
require_once "controllers/routers.controllers.php";
require_once "controllers/get.controller.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

//validamos cuando no se hace una peticion a la api
if (count($routesArray) == 0) {
    $json = array(
        "status" => 404,
        "results" => "Not Found"
    );

    echo json_encode($json, http_response_code($json["status"]));
    return;
}

//validamos cuando si se hace una peticion a la api
if (count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])) {
    $table = explode("?", $routesArray[1])[0]; // $routesArray[1][0]
    //TAREA COMPLETAR PARA VALIDAR LA LLAVE SECRETA "aoi key"


    //validacion
    $secret_key = Connection::apikey();
    $headers = apache_request_headers();

    if (in_array($table, (Connection::publicAccess()))) {
        
        $response = new GetController();
        $response->getData($table, "*", null, null, null, null);

        // validamos cuando se hace una petición GET
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            include "Services/get.php";
        }
        // validamos cuando se hace una petición POST
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            include "Services/post.php";
        }
        // validamos cuando se hace una petición PUT
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {
            include "Services/put.php";
        }
        // validamos cuando se hace una petición DELETE
        if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
            include "Services/delete.php";
        }
    } else {
        if (!isset($headers['Authorization']) || $headers['Authorization'] != $secret_key) {
            $json = array(
                "status" => 401,
                "results" => "Unauthorized"
            );
            echo json_encode($json);
            http_response_code($json['status']);

            return;
        } else {
            $respose = new GetController();
            /* $respose->getData($table, "*", null, null, null, null);
 */
            //validamos cuando se hace una peticion GET
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                include "Services/get.php";
            }
            //validamos cuando se hace una peticion POST
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                include "Services/post.php";
            }
            //validamos cuando se hace una peticion PUT
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                include "Services/put.php";
            }
            //validamos cuando se hace una peticion DELETE
            if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
                include "Services/delete.php";
            }
        }
    }
}
?>