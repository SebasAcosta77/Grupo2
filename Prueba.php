<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

<<<<<<< HEAD
function jsonResponse($status, $result) {
    http_response_code($status);
    echo json_encode(array(
        "status" => $status,
        "result" => $result
    ));
}

if (!empty($_GET["table"]) && !empty($_GET["id"]) && !empty($_GET["nameId"])) {
=======
if (isset($_GET["id"]) && isset($_GET["nameId"])) {
>>>>>>> parent of 9376664 (02-10-24-1pm)
    $table = $_GET["table"];
    $id = $_GET["id"];
    $nameId = $_GET["nameId"];

    // Check if the column exists in the table
    $columns = array($nameId);
    if (empty(Connection::getColumnsData($table, $columns))) {
<<<<<<< HEAD
        jsonResponse(400, "Los datos no coinciden");
=======
        $json = array(
            "status" => 400,
            "result" => "Los datos no coinciden"
        );
        echo json_encode($json, http_response_code($json["status"]));
>>>>>>> parent of 9376664 (02-10-24-1pm)
        return;
    }

    $response = DeleteController::deleteData($table, $id, $nameId);

    if ($response) {
<<<<<<< HEAD
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
=======
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
>>>>>>> parent of 9376664 (02-10-24-1pm)
?>
