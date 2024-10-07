<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

use Firebase\JWT\JWT;

define('HTTP_OK', 200);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_NOT_FOUND', 404);

/** Separar las propiedades del arreglo */
$columns = array_keys($_POST);

/** Validamos las tablas y las columnas */
if (empty(Connection::getColumnsData($table, $columns))) {
    echo json_encode([
        'status' => HTTP_BAD_REQUEST,
        'message' => "Los nombres de los campos de la base de datos no coinciden"
    ], http_response_code(HTTP_BAD_REQUEST));
    return;
}

$response = new PostController();

/** Manejo de registro y login */
if (isset($_GET["register"]) && $_GET["register"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
    return;
}

if (isset($_GET["login"]) && $_GET["login"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
    return;
}

/** Validar el token JWT */
$token = $_POST['token'] ?? null;
if ($token) {
    try {
        $decoded = JWT::decode($token, $secret_key, ['HS256']);
    } catch (\Firebase\JWT\ExpiredException $e) {
        echo json_encode([
            'status' => HTTP_UNAUTHORIZED,
            'message' => 'El token ha expirado'
        ], http_response_code(HTTP_UNAUTHORIZED));
        return;
    } catch (Exception $e) {
        echo json_encode([
            'status' => HTTP_UNAUTHORIZED,
            'message' => 'Token inválido'
        ], http_response_code(HTTP_UNAUTHORIZED));
        return;
    }
}

/** Manejo de recuperación de contraseña */
if (isset($_GET["recovery"]) && $_GET["recovery"] == true) {
    $email = $_POST["email_user"] ?? null;
    if ($email) {
        $response->postRecoveryResponse($email);
    } else {
        echo json_encode(['status' => HTTP_BAD_REQUEST, 'message' => 'Email es requerido'], http_response_code(HTTP_BAD_REQUEST));
    }
}
