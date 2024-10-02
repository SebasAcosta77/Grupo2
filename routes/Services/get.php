<?php

require_once "controllers/get.controller.php";
require_once "models/getModel.php";

// Parámetros
$select = $_GET["select"] ?? "*";
$table = $_GET["table"] ?? null;
$orderBy = $_GET["orderBy"] ?? null;
$orderMode = $_GET["orderMode"] ?? "ASC";
$startAt = $_GET["startAt"] ?? 0;  // Usar 0 por defecto para la paginación
$endAt = $_GET["endAt"] ?? 10;     // Usar 10 por defecto si no se proporciona
$filterTo = $_GET["filterTo"] ?? null;
$inTo = $_GET["inTo"] ?? null;

// Verificamos que se haya enviado el nombre de la tabla
//if ($table === null) {
    //echo json_encode([
        //"status" => 400,
        //"message" => "El parámetro 'table' es requerido"
    //]);
    return;
//}

// Instanciamos el modelo
$model = new GetController();
$response = $model->getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);


// Verificamos si se obtuvo un resultado
if (!empty($response)) {
    echo json_encode([
        "status" => 200,
        "data" => $response
    ]);
} else {
    echo json_encode([
        "status" => 404,
        "message" => "No se encontraron datos"
    ]);
}
