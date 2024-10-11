<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*=============================================
    Separar propiedades en un arreglo
    =============================================*/
    $columns = array_keys($_POST);

    /*=============================================
    Validar la tabla y las columnas
    =============================================*/
    if (empty(Connection::getColumnsData($table, $columns))) {
        $json = array(
            'status' => 400,
            'results' => "Error: Los campos del formulario no coinciden con la base de datos"
        );

        echo json_encode($json);
        http_response_code($json["status"]);
        return;
    }

    $response = new PostController();

    /*=============================================
    Peticion POST para registrar usuario
    =============================================*/
    if (isset($_GET["register"]) && $_GET["register"] === "true") {
        $suffix = $_GET["suffix"] ?? "user";
        $response->postRegister($table, $_POST, $suffix);

    /*=============================================
    Peticion POST para login de usuario
    =============================================*/
    } elseif (isset($_GET["login"]) && $_GET["login"] === "true") {
        $suffix = $_GET["suffix"] ?? "user";
        $response->postLogin($table, $_POST, $suffix);

    } else {
        if (isset($_GET["token"])) {
            /*=============================================
            Peticion POST para usuarios no autorizados
            =============================================*/
            if ($_GET["token"] === "no" && isset($_GET["except"])) {
                $columns = array($_GET["except"]);

                if (empty(Connection::getColumnsData($table, $columns))) {
                    $json = array(
                        'status' => 400,
                        'results' => "Error: Los campos en el formulario no coinciden con el token"
                    );

                    echo json_encode($json);
                    http_response_code($json["status"]);
                    return;
                }

                /*=============================================
                Solicitar respuesta del controlador para crear datos en cualquier tabla
                =============================================*/
                $response->postData($table, $_POST);

            /*=============================================
            Peticion POST para usuarios autorizados
            =============================================*/
            } else {
                $tableToken = $_GET["table"] ?? "users";
                $suffix = $_GET["suffix"] ?? "user";

                $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);

                /*=============================================
                Solicitar respuesta del controlador para crear datos en cualquier tabla
                =============================================*/
                if ($validate === "ok") {
                    $response->postData($table, $_POST);
                }

                /*=============================================
                Error cuando el token ha expirado
                =============================================*/
                if ($validate === "expired") {
                    $json = array(
                        'status' => 303,
                        'results' => "Error: El token ha expirado"
                    );

                    echo json_encode($json);
                    http_response_code($json["status"]);
                    return;
                }

                /*=============================================
                Error cuando el token no coincide en BD
                =============================================*/
                if ($validate === "no-auth") {
                    $json = array(
                        'status' => 400,
                        'results' => "Error: El usuario no está autorizado"
                    );

                    echo json_encode($json);
                    http_response_code($json["status"]);
                    return;
                }
            }

        /*=============================================
        Error cuando no envía token
        =============================================*/
        } else {
            $json = array(
                'status' => 400,
                'results' => "Error: Se requiere autorización"
            );

            echo json_encode($json);
            http_response_code($json["status"]);
            return;	
        }	
    }
}
