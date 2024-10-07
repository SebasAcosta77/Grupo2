<?php
require_once "vendor/autoload.php";
require_once "models/postModel.php";

use Firebase\JWT\JWT;

class PostController
{
    /* Función generalizada para registro y login */
    static public function postAuthResponse($table, $data, $suffix, $isLogin = false)
    {
        // En caso de login
        if ($isLogin) {
            $response = GetModel::getRelDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null, null);
            // Verificamos si el usuario existe
            if (!empty($response)) {
                // Verificar contraseña
                if (isset($response[0]->{"password_" . $suffix}) && password_verify($data["password_" . $suffix], $response[0]->{"password_" . $suffix})) {
                    // Generar token JWT si la contraseña es correcta
                    $token = Connection::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"email_" . $suffix});
                    $jwt = JWT::encode($token, "a2%4ndjle$%&ashbdajs-5avs");

                    // Actualizar token y fecha de expiración en la base de datos
                    $updateData = array(
                        "token_" . $suffix => $jwt,
                        "token_exp_" . $suffix => $token["exp"]
                    );
                    $update = PutModel::putData($table, $updateData, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                    if (isset($update["comentario"]) && $update["comentario"] == "el proceso fue satisfactorio") {
                        $response[0]->{"token_" . $suffix} = $jwt;
                        $response[0]->{"token_exp_" . $suffix} = $token["exp"];
                    }

                    // Retornar respuesta de éxito
                    return self::fncResponse($response, null, $suffix);
                } else {
                    // Contraseña incorrecta
                    return self::fncResponse(null, "Contraseña incorrecta", $suffix);
                }
            } else {
                // Usuario no encontrado
                return self::fncResponse(null, "Usuario no encontrado", $suffix);
            }
        } else {
            // Caso de registro
            // Generar un código de recuperación aleatorio de 6 dígitos
            $data["codigo_recuperacion_" . $suffix] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Encriptar contraseña si está presente
            if (isset($data["password_" . $suffix]) && !empty($data["password_" . $suffix])) {
                $data["password_" . $suffix] = password_hash($data["password_" . $suffix], PASSWORD_BCRYPT);
            }

            // Llamamos al modelo para registrar los datos
            $response = PostModel::postData($table, $data);

            // Retornar respuesta de éxito
            return self::fncResponse($response, null, $suffix);
        }
    }

    /* Función para generar un nuevo código de recuperación */
    public static function generateRecoveryCode($table, $email, $suffix, $recuperacion)
    {
        // Buscar usuario por email
        $response = GetModel::getRelDataFilter($table, "*", "email_" . $suffix, $email, null, null, null, null, null); // Cambiado $data a $email
        if (!empty($response)) {
            if ($recuperacion == true) {
                // Generar un nuevo código de recuperación de 6 dígitos
                $newCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                // Actualizar el código de recuperación en la base de datos
                $updateData = array(
                    "codigo_recuperacion_" . $suffix => $newCode
                );
                $update = PutModel::putData($table, $updateData, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                if (isset($update["comentario"]) && $update["comentario"] == "el proceso fue satisfactorio") {
                    // Enviar respuesta con el nuevo código de recuperación
                    $response[0]->{"codigo_recuperacion_" . $suffix} = $newCode;
                    return self::fncResponse($response, null, $suffix);
                } else {
                    return self::fncResponse(null, "Error al actualizar el código de recuperación", $suffix);
                }
            }
        } else {
            return self::fncResponse(null, "Usuario no encontrado", $suffix);
        }
    }

    /* Función para manejar la solicitud de recuperación de contraseña */
    public function postPasswordRecoveryRequest($table, $data, $suffix)
    {
        if (!isset($data["email_" . $suffix])) {
            self::fncResponse(null, "Correo requerido", $suffix);
            return;
        }

        $email = $data["email_" . $suffix];
        error_log("Buscando usuario con el email: $email");

        $user = GetModel::getDataFilter($table, "*", "email_" . $suffix, $email, null, null, null, null);

        if ($user === null) {
            self::fncResponse(null, "Error al obtener el usuario", $suffix);
            return;
        }

        if (!empty($user)) {
            // Resto del código para generar y enviar el código de recuperación...
        } else {
            self::fncResponse(null, "Email no encontrado", $suffix);
        }
    }


    /* Función para restablecer la contraseña */
    public function resetPassword($table, $data, $suffix)
    {
        // Verificar que el código de recuperación y la nueva contraseña están presentes
        if (!isset($data["reset_code_" . $suffix]) || !isset($data["new_password_" . $suffix])) {
            return self::fncResponse(null, "Código de recuperación y nueva contraseña requeridos", $suffix);
        }

        // Buscar al usuario por email y el código de recuperación
        $response = GetModel::getRelDataFilter($table, "*", "reset_code_" . $suffix, $data["reset_code_" . $suffix], null, null, null, null, null);

        if (!empty($response)) {
            // Verificar si el código de recuperación ha expirado
            $expirationDate = $response[0]->{"reset_code_exp_" . $suffix};
            if (strtotime($expirationDate) < time()) {
                return self::fncResponse(null, "El código de recuperación ha expirado", $suffix);
            }

            // Encriptar la nueva contraseña
            $data["new_password_" . $suffix] = password_hash($data["new_password_" . $suffix], PASSWORD_BCRYPT);

            // Actualizar la contraseña en la base de datos
            $updateData = array(
                "password_" . $suffix => $data["new_password_" . $suffix],
                "reset_code_" . $suffix => null, // Limpiar el código de recuperación
                "reset_code_exp_" . $suffix => null, // Limpiar la fecha de expiración
                "reset_attempts_" . $suffix => 0 // Reiniciar los intentos
            );

            $update = PutModel::putData($table, $updateData, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

            if (isset($update["comentario"]) && $update["comentario"] == "el proceso fue satisfactorio") {
                return self::fncResponse(null, "Contraseña actualizada con éxito", $suffix);
            } else {
                return self::fncResponse(null, "Error al actualizar la contraseña", $suffix);
            }
        } else {
            return self::fncResponse(null, "Código de recuperación no válido", $suffix);
        }
    }

    /* Función para responder JSON */
    public static function fncResponse($response, $error, $suffix)
    {
        header('Content-Type: application/json'); // Establecer tipo de contenido JSON
        if (!empty($response) && is_array($response) && isset($response[0])) {
            if (isset($response[0]->{"password_" . $suffix})) {
                unset($response[0]->{"password_" . $suffix});
            }
            $json = array(
                'status' => 200,
                'result' => $response
            );
            http_response_code(200);
        } else {
            $json = array(
                'status' => 400,
                'result' => $error ?? 'Error en la solicitud'
            );
            http_response_code(400);
        }
        echo json_encode($json);
    }
}
