<?php
require_once "vendor/autoload.php";
require_once "models/getModel.php";

class GetController
{

    /*peticiones get sin filtro*/
    static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
    {
        // Depuración: Verifica los parámetros recibidos
        //echo "table: $table, select: $select, orderBy: $orderBy, orderMode: $orderMode, startAt: $startAt, endAt: $endAt<br>";

        $response = GetModel::getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);
        $return = new GetController();
        $return->fncResponse($response);
    }

    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        // Depuración: Verifica los parámetros recibidos
        echo "table: $table, select: $select, linkTo: $linkTo, equalTo: $equalTo, orderBy: $orderBy, orderMode: $orderMode, startAt: $startAt, endAt: $endAt<br>";

        $response = GetModel::getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
        $return = new GetController();
        $return->fncResponse($response);
    }


    /*peticiones get entre tablas relacionadas*/
    static public function getRelData($rel, $type, $select, $equalTo, $orderderBy, $orderMode, $startAt, $endAt)
    {
        $response = GetModel::getRelData($rel, $type, $select, $equalTo, $orderderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*peticiones get entre tablas relaciones con filtro */
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderderBy, $orderMode, $startAt, $endAt)
    {
        $response = GetModel::getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*peticiones get para busqueda de datos*/
    static public function getDataSearch($tabla, $select, $linkTo, $search, $orderderBy, $orderMode, $startAt, $endAt)
    {
        $response = GetModel::getDataSearch($tabla, $select, $linkTo, $search, $orderderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*peticiones get para busqueda entre tablas relacionadas*/
    static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderderBy, $orderMode, $startAt, $endAt)
    {
        $response = GetModel::getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);
    }
    /*peticiones get para filtrar por rango*/
    static public function getDataRange($table, $select,  $linkTo, $between1, $between2, $endValue, $searchValue, $orderderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {
        $response = GetModel::getDataRange($table, $select,  $linkTo, $between1, $between2, $endValue, $searchValue, $orderderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

        $return = new GetController();
        $return->fncResponse($response);
    }

    public function fncResponse($response)
    {
        // Determinar la respuesta y el código de estado
        if (!empty($response)) {
            $json = array(
                'status' => 200,
                'total' => count($response),
                'results' => $response
            );
            http_response_code(200); // Establecer el código de estado HTTP
        } else {
            $json = array(
                'status' => 404,
                'results' => 'Not Found',
                'method' => 'get'
            );
            http_response_code(404); // Establecer el código de estado HTTP
        }

        // Enviar la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
