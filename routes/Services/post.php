<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

use Firebase\JWT\JWT;

/**Separar las propiedades del arreglo */
$columns = array();
foreach (array_keys($_POST) as $key => $value) { // Cambié $array_keys a array_keys
    array_push($columns, $value); // Cambié $values a $value
}

/**Validamos las tablas y las columnas */
if (empty(Connection::getColumnsData($table, $columns))) {
    $json = array(
        'result' => 400,
        'message' => "Los nombres de los campos de la base de datos no coinciden" // Cambié 'result' a 'message'
    );

    echo json_encode($json, http_response_code($json['result']));
    return;
}

$response = new PostController();
/**Peticiones post para registro de usuarios */
if (isset($_GET["register"]) && $_GET["register"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
} else if (isset($_GET["login"]) && $_GET["login"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
} else {
    /**Validar cuando el token de usuario jwt es inválido */
    $token = isset($_POST['token']) ? $_POST['token'] : null;
    if ($token) {
        try {
            $decoded = JWT::decode($token, $secret_key, array('HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            $json = array(
                'result' => 401,
                'message' => 'El token ha expirado'
            );
            echo json_encode($json, http_response_code($json['result']));
            return;
        } catch (Exception $e) {
            $json = array(
                'result' => 401,
                'message' => 'Token inválido'
            );
            echo json_encode($json, http_response_code($json['result']));
            return;
        }
    }

    /**Peticiones post para usuarios no autorizados */
    /**Peticiones post para usuarios autorizados */
    /**Validar cuando el token expiro */
    /**Validar cuando el token no coincide con el de la base de datos */
}

// Services/post.php

if (isset($_GET["recovery"]) && $_GET["recovery"] == true) {
    $email = $_POST["email_user"] ?? null;
    if ($email) {
        $response->postRecoveryResponse($email);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Email es requerido']);
    }
}
