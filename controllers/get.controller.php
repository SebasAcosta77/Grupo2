<?php

require_once "models/get.model.php";

class GetController {

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public static function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($table, $select, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET con filtro
    =============================================*/
    public static function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET sin filtro entre tablas relacionadas
    =============================================*/
    public static function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET con filtro entre tablas relacionadas
    =============================================*/
    public static function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET para el buscador sin relaciones
    =============================================*/
    public static function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET para el buscador entre tablas relacionadas
    =============================================*/
    public static function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt) {
        self::handleGetRequest(function() use ($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt) {
            return GetModel::getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);
        });
    }

    /*=============================================
    Peticiones GET para selección de rangos
    =============================================*/
    public static function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo) {
        self::handleGetRequest(function() use ($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo) {
            return GetModel::getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
        });
    }

    /*=============================================
    Peticiones GET para selección de rangos con relaciones
    =============================================*/
    public static function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo) {
        self::handleGetRequest(function() use ($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo) {
            return GetModel::getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
        });
    }

    /*=============================================
    Método privado para manejar respuestas
    =============================================*/
    private static function handleGetRequest($callback) {
        try {
            $response = $callback();
            if (!empty($response)) {
                self::sendResponse(200, count($response), $response);
            } else {
                self::sendResponse(404, 0, "Not Found");
            }
        } catch (Exception $e) {
            self::sendResponse(500, 0, "Internal Server Error: " . $e->getMessage());
        }
    }

    /*=============================================
    Enviar respuestas del controlador
    =============================================*/
    private static function sendResponse($statusCode, $total, $results) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => $statusCode,
            'total' => $total,
            'results' => $results
        ]);
        exit; // Detiene la ejecución para evitar más salida de datos
    }
}
