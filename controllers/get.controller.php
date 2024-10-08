<?php

require_once "models/get.model.php";

class GetController
{
    /* Función genérica para manejar peticiones GET */
    private static function handleRequest($method, ...$params)
    {
        $response = GetModel::$method(...$params);
        self::fncResponse($response);
    }

<<<<<<< HEAD
	/*=============================================
	Peticiones GET sin filtro
	=============================================*/

	static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
	{

		$response = GetModel::getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET con filtro
	=============================================*/

	static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
	{

		$response = GetModel::getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET sin filtro entre tablas relacionadas
	=============================================*/

	static public function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt)
	{

		$response = GetModel::getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}


	/*=============================================
	Peticiones GET con filtro entre tablas relacionadas
	=============================================*/

	static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
	{

		$response = GetModel::getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET para el buscador sin relaciones
	=============================================*/

	static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
	{

		$response = GetModel::getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET para el buscador entre tablas relacionadas
	=============================================*/

	static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
	{
=======
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
>>>>>>> parent of 4914481 (SIIIIIII)

		$response = GetModel::getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET para selección de rangos
	=============================================*/

	static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
	{

		$response = GetModel::getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Peticiones GET para selección de rangos con relaciones
	=============================================*/

	static public function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
	{

		$response = GetModel::getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

		$return = new GetController();
		$return->fncResponse($response);
	}

	/*=============================================
	Respuestas del controlador
	=============================================*/

	public function fncResponse($response)
	{

		if (!empty($response)) {

			$json = array(

				'status' => 200,
				'total' => count($response),
				'results' => $response

			);
		} else {

			$json = array(

				'status' => 404,
				'results' => 'Not Found',
				'method' => 'get'

			);
		}

		echo json_encode($json, http_response_code($json["status"]));
	}
}
