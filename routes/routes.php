<?php

require_once "Models/Connection.php";
require_once "Controllers/routers.controllers.php";
require_once "Controllers/get.controller.php";
require_once "Controllers/VerifyEmailController.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

// Validamos cuando no se hace una petición a la API
if (count($routesArray) == 0) {
    $json = array(
        "status" => 404,
        "results" => "Not Found"
    );

    echo json_encode($json, http_response_code($json["status"]));
    return;
}

// Validamos cuando sí se hace una petición a la API
if (count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])) {
    $table = explode("?", $routesArray[1])[0]; // $routesArray[1][0]

    // Validamos el secret key
    $secret_key = Connection::apikey();
    $headers = apache_request_headers();

    if (in_array($table, (Connection::publicAccess()))) {

        // Validamos cuando se hace una petición GET
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $response = new GetController();
            $response->getData($table, "*", null, null, null, null);
            include "Services/get.php";
        }

        // Validamos cuando se hace una petición POST
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // Verificar si la petición es para validar email y generar código de verificación
            if ($table == "verify") {
                // Obtenemos el cuerpo de la petición
                $data = json_decode(file_get_contents("php://input"), true);

                // Validamos que el email esté presente en los datos
                if (isset($data['email'])) {
                    $email = $data['email'];

                    // Llamamos al controlador de verificación
                    $verifyController = new VerifyEmailController();
                    $verifyController->sendVerificationCode($email);
                } else {
                    $json = array(
                        "status" => 400,
                        "results" => "Bad Request: Email is missing."
                    );
                    echo json_encode($json);
                    http_response_code($json['status']);
                }
            } else {
                // Verificar si la petición es para cambiar la contraseña
                if ($table == "change-password") {
                    // Obtenemos el cuerpo de la petición
                    $data = json_decode(file_get_contents("php://input"), true);

                    // Validamos que el email, código y nueva contraseña estén presentes en los datos
                    if (isset($data['email']) && isset($data['code']) && isset($data['newPassword'])) {
                        $email = $data['email'];
                        $code = $data['code'];
                        $newPassword = $data['newPassword'];

                        // Llamamos al controlador de verificación
                        $verifyController = new VerifyEmailController();
                        $response = $verifyController->changePassword($email, $code, $newPassword);

                        // Enviamos la respuesta
                        echo $response;
                    } else {
                        $json = array(
                            "status" => 400,
                            "results" => "Bad Request: Missing required fields."
                        );
                        echo json_encode($json);
                        http_response_code($json['status']);
                    }
                }
            }
        } else {
            include "Services/post.php";
        }

        // Validamos cuando se hace una petición PUT
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {
            include "Services/put.php";
        }

        // Validamos cuando se hace una petición DELETE
        if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
            include "Services/delete.php";
        }
    } else {
        // Validación del header 'Authorization'
        if (!isset($headers['Authorization']) || $headers['Authorization'] != $secret_key) {
            $json = array(
                "status" => 401,
                "results" => "Unauthorized"
            );
            echo json_encode($json);
            http_response_code($json['status']);
            return;
        } else {
            $response = new GetController();
            $response->getData($table, "*", null, null, null, null);

            // Validamos cuando se hace una petición GET
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                include "Services/get.php";
            }

            // Validamos cuando se hace una petición POST
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                include "Services/post.php";
            }

            // Validamos cuando se hace una petición PUT
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                include "Services/put.php";
            }

            // Validamos cuando se hace una petición DELETE
            if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
                include "Services/delete.php";
            }
        }
    }
}
