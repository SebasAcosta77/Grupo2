<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

function jsonResponse($status, $result) {
    http_response_code($status);
    echo json_encode(array(
        "status" => $status,
        "result" => $result
    ));
}

if (!empty($_GET["table"]) && !empty($_GET["id"]) && !empty($_GET["nameId"])) {
    $table = $_GET["table"];
    $id = $_GET["id"];
    $nameId = $_GET["nameId"];

    // Check if the column exists in the table
    $columns = array($nameId);
    if (empty(Connection::getColumnsData($table, $columns))) {
        jsonResponse(400, "Los datos no coinciden");
        return;
    }

    $response = DeleteController::deleteData($table, $id, $nameId);

    if ($response) {
        jsonResponse(200, "El registro ha sido eliminado");
    } else {
        jsonResponse(400, "No se pudo eliminar el registro");
    }
} else {
    jsonResponse(400, "Faltan parámetros");
}
<<<<<<< HEAD:routes/Services/delete.php

/** Tarea: Validar el token si coincide o no con el almacenado en la base de datos */

=======
>>>>>>> parent of 9376664 (02-10-24-1pm):Prueba.php
?>
