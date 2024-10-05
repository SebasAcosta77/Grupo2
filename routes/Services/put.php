<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

define('HTTP_OK', 200);
define('HTTP_BAD_REQUEST', 400);

if (isset($_GET["id"]) && isset($_GET["nameId"])) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que $data no esté vacío
    if (empty($data)) {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "Los datos de entrada están vacíos"
        ], http_response_code(HTTP_BAD_REQUEST));
        return;
    }

    $columns = array_keys($data);
    array_push($columns, $_GET["nameId"]);
    $columns = array_unique($columns);

    // Validar que los nombres de columna coincidan
    if (empty(Connection::getColumnsData($table, $columns))) {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "Los datos no coinciden"
        ], http_response_code(HTTP_BAD_REQUEST));
        return;
    }

    // Aquí iría la lógica para actualizar los datos usando PutController
    $response = PutController::putModel($table, $data, $_GET["id"], $_GET["nameId"]);
    
    if ($response) {
        echo json_encode([
            "status" => HTTP_OK,
            "result" => "Datos actualizados correctamente"
        ], http_response_code(HTTP_OK));
    } else {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "No se pudieron actualizar los datos"
        ], http_response_code(HTTP_BAD_REQUEST));
    }
} else {
    echo json_encode([
        "status" => HTTP_BAD_REQUEST,
        "result" => "Faltan parámetros"
    ], http_response_code(HTTP_BAD_REQUEST));
}

?>
