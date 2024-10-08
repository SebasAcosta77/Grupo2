<?php

require_once "controllers/get.controller.php";
require_once "models/connection.php";

$select = $_GET["select"] ?? "*";
$orderBy = $_GET["orderBy"] ?? null;
$orderMode = $_GET["orderMode"] ?? null;
$startAt = $_GET["startAt"] ?? null;
$endAt = $_GET["endAt"] ?? null;
$filterTo = $_GET["filterTo"] ?? null;
$inTo = $_GET["inTo"] ?? null;

<<<<<<< HEAD
$response = new GetController();

/*=============================================
Peticiones GET con filtro
=============================================*/

if (isset($_GET["linkTo"]) && isset($_GET["equalTo"]) && !isset($_GET["rel"]) && !isset($_GET["type"])) {

	$response->getDataFilter($table, $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);

	/*=============================================
Peticiones GET sin filtro entre tablas relacionadas
=============================================*/
} else if (isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && !isset($_GET["linkTo"]) && !isset($_GET["equalTo"])) {

	$response->getRelData($_GET["rel"], $_GET["type"], $select, $orderBy, $orderMode, $startAt, $endAt);

	/*=============================================
Peticiones GET con filtro entre tablas relacionadas
=============================================*/
} else if (isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["equalTo"])) {

	$response->getRelDataFilter($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);

	/*=============================================
Peticiones GET para el buscador sin relaciones
=============================================*/
} else if (!isset($_GET["rel"]) && !isset($_GET["type"]) && isset($_GET["linkTo"]) && isset($_GET["search"])) {

	$response->getDataSearch($table, $select, $_GET["linkTo"], $_GET["search"], $orderBy, $orderMode, $startAt, $endAt);

	/*=============================================
Peticiones GET para el buscador con relaciones
=============================================*/
} else if (isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["search"])) {


	$response->getRelDataSearch($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["search"], $orderBy, $orderMode, $startAt, $endAt);

	/*=============================================
Peticiones GET para selección de rangos
=============================================*/
} else if (!isset($_GET["rel"]) && !isset($_GET["type"]) && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])) {

	$response->getDataRange($table, $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

	/*=============================================
Peticiones GET para selección de rangos con relaciones
=============================================*/
} else if (isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])) {

	$response->getRelDataRange($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
} else {

	/*=============================================
	Peticiones GET sin filtro
	=============================================*/

	$response->getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);
=======
// Verificamos que se haya enviado el nombre de la tabla
if ($table === null) {
    echo json_encode([
        "status" => 400,
        "message" => "El parámetro 'table' es requerido"
    ]);
    http_response_code(400); // Establecer código de respuesta HTTP
    return;
}

// Instanciamos el controlador
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
>>>>>>> parent of 4914481 (SIIIIIII)
}
