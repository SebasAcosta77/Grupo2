<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

use Firebase\JWT\JWT;

define('HTTP_OK', 200);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_NOT_FOUND', 404);

/** Asegurarse de que el parámetro 'table' esté definido */
$table = $_GET['table'] ?? null;

if (!$table) {
    echo json_encode([
        'status' => HTTP_BAD_REQUEST,
        'message' => "El parámetro 'table' es requerido"
    ], http_response_code(HTTP_BAD_REQUEST));
    return;
}

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
    $sufix = $_GET["sufix"] ?? "user";
    $response->postPasswordRecoveryRequest($table, $_POST, $sufix);
    return;
}

// Restablecer contraseña usando el código
if (isset($_GET["reset"]) && $_GET["reset"] == true) {
    $code = $_POST['code'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    
    if ($code && $newPassword) {
        $response->resetPassword($table, $code, $newPassword, $sufix);
    } else {
        echo json_encode(['status' => HTTP_BAD_REQUEST, 'message' => 'Código y nueva contraseña requeridos'], http_response_code(HTTP_BAD_REQUEST));
    }
    return;
}
