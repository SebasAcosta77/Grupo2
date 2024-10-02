<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

if (isset($_GET["id"]) && isset($_GET["nameId"])) {
    $table = $_GET["table"];
    $id = $_GET["id"];
    $nameId = $_GET["nameId"];

    // Check if the column exists in the table
    $columns = array($nameId);
    if (empty(Connection::getColumnsData($table, $columns))) {
        $json = array(
            "status" => 400,
            "result" => "Los datos no coinciden"
        );
        echo json_encode($json, http_response_code($json["status"]));
        return;
    }

    $response = DeleteController::deleteData($table, $id, $nameId);

    if ($response) {
        $json = array(
            "status" => 200,
            "result" => "El registro ha sido eliminado"
        );
    } else {
        $json = array(
            "status" => 400,
            "result" => "No se pudo eliminar el registro"
        );
    }

    echo json_encode($json, http_response_code($json["status"]));
} else {
    $json = array(
        "status" => 400,
        "result" => "Faltan parámetros"
    );
    echo json_encode($json, http_response_code($json["status"]));
}
?>
