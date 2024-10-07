<?php

require_once "controllers/get.controller.php";
require_once "models/getModel.php";

// Parámetros
$select = $_GET["select"] ?? "*";  // Seleccionar todas las columnas si no se especifica
$table = $_GET["table"] ?? null;
$orderBy = $_GET["orderBy"] ?? null;
$orderMode = $_GET["orderMode"] ?? "ASC";
$startAt = $_GET["startAt"] ?? 0;  // Usar 0 por defecto para la paginación
$endAt = $_GET["endAt"] ?? 10;     // Usar 10 por defecto si no se proporciona

// Verificamos que se haya enviado el nombre de la tabla
if ($table === null) {
    echo json_encode([
        "status" => 400,
        "message" => "El parámetro 'table' es requerido"
    ]);
    http_response_code(400); // Establecer código de respuesta HTTP
    return;
}

// Validación adicional para evitar inyección SQL
$validTables = ['libros', 'usuarios']; // Agrega aquí las tablas válidas
if (!in_array($table, $validTables)) {
    echo json_encode([
        "status" => 400,
        "message" => "La tabla especificada no es válida"
    ]);
    http_response_code(400); // Establecer código de respuesta HTTP
    return;
}

// Instanciamos el controlador de datos
$model = new GetController();
$response = $model->getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);

// Verificamos si se obtuvo un resultado
if (!empty($response)) {
    echo json_encode([
        "status" => 200,
        "data" => $response
    ]);
    http_response_code(200); // Establecer código de respuesta HTTP
} else {
    echo json_encode([
        "status" => 404,
        "message" => "No se encontraron datos"
    ]);
    http_response_code(404); // Establecer código de respuesta HTTP
}
?>
