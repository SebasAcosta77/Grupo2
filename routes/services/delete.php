<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

if (isset($_GET["id"]) && isset($_GET["nameId"])) {
    $columns = array($_GET["nameId"]);
    $table = $_GET["table"]; // Asegúrate de que la tabla está definida

    /*=============================================
    Validar la tabla y las columnas
    =============================================*/
    if (empty(Connection::getColumnsData($table, $columns))) {
        $json = array(
            'status' => 400,
            'results' => "Error: Fields in the form do not match the database"
        );

        echo json_encode($json, http_response_code($json["status"]));
        return;
    }

    /*=============================================
    Peticion DELETE para usuarios autorizados
    =============================================*/
    if (isset($_GET["token"])) {
        $tableToken = $_GET["table"] ?? "users";
        $suffix = $_GET["suffix"] ?? "user";
        $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);

        /*=============================================
        Solicitar respuesta del controlador para eliminar datos en cualquier tabla
        =============================================*/	
        if ($validate == "ok") {
            $response = new DeleteController();
            $response->deleteData($table, $_GET["id"], $_GET["nameId"]);
        } elseif ($validate == "expired") {
            $json = array(
                'status' => 303,
                'results' => "Error: The token has expired"
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        } elseif ($validate == "no-auth") {
            $json = array(
                'status' => 400,
                'results' => "Error: The user is not authorized"
            );
            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    } else {
        /*=============================================
        Error cuando no envía token
        =============================================*/	
        $json = array(
            'status' => 400,
            'results' => "Error: Authorization required"
        );

        echo json_encode($json, http_response_code($json["status"]));
        return;	
    }	
} else {
    // Manejo de error si no se proporciona id o nameId
    $json = array(
        'status' => 400,
        'results' => "Error: Missing required parameters (id, nameId)"
    );
    echo json_encode($json, http_response_code($json["status"]));
    return;
}
