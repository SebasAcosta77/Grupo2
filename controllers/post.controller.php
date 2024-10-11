<?php 

require_once "models/get.model.php";
require_once "models/post.model.php";
require_once "models/connection.php";
require_once "vendor/autoload.php";
use Firebase\JWT\JWT;

require_once "models/put.model.php";

class PostController {

    /*=============================================
    Peticion POST para crear datos
    =============================================*/

    static public function postData($table, $data) {
        $response = PostModel::postData($table, $data);
        self::fncResponse($response, null, null);
    }

    /*=============================================
    Peticion POST para registrar usuario
    =============================================*/

    static public function postRegister($table, $data, $suffix) {
        if (isset($data["password_" . $suffix]) && !empty($data["password_" . $suffix])) {
            // Hashear la contraseña
            $data["password_" . $suffix] = password_hash($data["password_" . $suffix], PASSWORD_BCRYPT);
        }

        // Registrar usuario
        $response = PostModel::postData($table, $data);
        if (isset($response["comment"]) && $response["comment"] == "The process was successful") {
            self::handleToken($table, $data, $suffix);
        } else {
            self::fncResponse($response, null, $suffix);
        }
    }

    /*=============================================
    Manejo de Tokens
    =============================================*/
    
    private static function handleToken($table, $data, $suffix) {
        // Validar que el usuario exista en BD
        $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);
        
        if (!empty($response)) {
            $token = Connection::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"email_" . $suffix});
            $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

            // Actualizamos la base de datos con el Token del usuario
            $updateData = array(
                "token_" . $suffix => $jwt,
                "token_exp_" . $suffix => $token["exp"]
            );

            $update = PutModel::putData($table, $updateData, $response[0]->{"id_" . $suffix}, "id_" . $suffix);
            if (isset($update["comment"]) && $update["comment"] == "The process was successful") {
                $response[0]->{"token_" . $suffix} = $jwt;
                $response[0]->{"token_exp_" . $suffix} = $token["exp"];
                self::fncResponse($response, null, $suffix);
            }
        }
    }

    /*=============================================
    Peticion POST para login de usuario
    =============================================*/

    static public function postLogin($table, $data, $suffix) {
        // Validar que el usuario exista en BD
        $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);
        
        if (!empty($response)) {
            if (isset($response[0]->{"password_" . $suffix}) && password_verify($data["password_" . $suffix], $response[0]->{"password_" . $suffix})) {
                self::updateToken($table, $response[0], $suffix);
            } else {
                self::fncResponse(null, "Wrong password", $suffix);
            }
        } else {
            self::fncResponse(null, "Wrong email", $suffix);
        }
    }

    /*=============================================
    Actualizar Token
    =============================================*/

    private static function updateToken($table, $user, $suffix) {
        $token = Connection::jwt($user->{"id_" . $suffix}, $user->{"email_" . $suffix});
        $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

        $updateData = array(
            "token_" . $suffix => $jwt,
            "token_exp_" . $suffix => $token["exp"]
        );

        $update = PutModel::putData($table, $updateData, $user->{"id_" . $suffix}, "id_" . $suffix);
        if (isset($update["comment"]) && $update["comment"] == "The process was successful") {
            $user->{"token_" . $suffix} = $jwt;
            $user->{"token_exp_" . $suffix} = $token["exp"];
            self::fncResponse([$user], null, $suffix);
        }
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/

    public static function fncResponse($response, $error, $suffix) {
        if (!empty($response)) {
            // Quitamos la contraseña de la respuesta
            if (isset($response[0]->{"password_" . $suffix})) {
                unset($response[0]->{"password_" . $suffix});
            }
            self::sendResponse(200, $response);
        } else {
            self::sendResponse($error ? 400 : 404, $error ?? 'Not Found');
        }
    }

    private static function sendResponse($status, $results) {
        http_response_code($status);
        echo json_encode(array('status' => $status, 'results' => $results));
    }
}
