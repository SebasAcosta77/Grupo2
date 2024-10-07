<?php
require_once "vendor/autoload.php";
require_once "models/getModel.php";

class GetController
{
    /* Función genérica para manejar peticiones GET */
    private static function handleRequest($method, ...$params)
    {
        $response = GetModel::$method(...$params);
        self::fncResponse($response);
    }

    /* Peticiones GET sin filtro */
    static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getData', $table, $select, $orderBy, $orderMode, $startAt, $endAt);
    }

    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getDataFilter', $table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
    }

    /* Peticiones GET entre tablas relacionadas */
    static public function getRelData($rel, $type, $select, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getRelData', $rel, $type, $select, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
    }

    /* Peticiones GET entre tablas relaciones con filtro */
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getRelDataFilter', $rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
    }

    /* Peticiones GET para búsqueda de datos */
    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getDataSearch', $table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);
    }

    /* Peticiones GET para búsqueda entre tablas relacionadas */
    static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        self::handleRequest('getRelDataSearch', $rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);
    }

    /* Peticiones GET para filtrar por rango */
    static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {
        self::handleRequest('getDataRange', $table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
    }

    private static function fncResponse($response)
    {
        // Determinar la respuesta y el código de estado
        if (!empty($response)) {
            $json = [
                'status' => 200,
                'total' => count($response),
                'results' => $response,
            ];
            http_response_code(200); // Establecer el código de estado HTTP
        } else {
            $json = [
                'status' => 404,
                'results' => 'Not Found',
                'method' => 'get',
            ];
            http_response_code(404); // Establecer el código de estado HTTP
        }

        // Enviar la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
