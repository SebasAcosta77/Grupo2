<?php
require_once "vendor/autoload.php";
require_once "models/postModel.php";

use Firebase\JWT\JWT;

class PostController
{
    /*Función generalizada para registro y login*/
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

    public function postPasswordRecoveryRequest($table, $data, $suffix)
    {
        if (!isset($data["email_" . $suffix])) {
            $this->fncResponse(null, "correo requerido", $suffix);
            return;
        }

        $user = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);

        if (!empty($user)) {
            // Generar un código numérico aleatorio de 6 dígitos
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Establecer el tiempo de expiración a 1 hora desde ahora
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $updateData = [
                "reset_code_" . $suffix => $code,
                "reset_code_exp_" . $suffix => $expiration,
                "reset_attempts_" . $suffix => 0
            ];

            $update = PutModel::putData($table, $updateData, $user[0]->{"id_" . $suffix}, "id_" . $suffix);

            if (isset($update["comment"]) && $update["comment"] == "The process was successful") {
                // Crear una instancia de EmailSender
                $emailSender = new EmailSender();

                // Enviar el correo
                $emailSent = $emailSender->sendRecoveryCode($data["email_" . $suffix], $code);

                if ($emailSent) {

                    error_log("Email enviado correctamente");
                    $this->fncResponse([
                        "message" => "Recovery code sent to your email"
                    ], null, $suffix);
                } else {
                    $this->fncResponse(null, "Error enviado email", $suffix);
                }
            } else {
                $this->fncResponse(null, "Error actualizando usuario", $suffix);
            }
        } else {
            $this->fncResponse(null, "Email no encontrado", $suffix);
        }
    }


    public function postRecovery($table, $data)
    {
        $email = $data['email']; // Asegúrate de que el email esté en los datos enviados
        $response = PostModel::sendRecoveryCode($email);

        echo json_encode($response, http_response_code($response['status']));
    }


    /** Método para manejar la recuperación de contraseña */
    public function postRecoveryResponse($email)
    {
        $response = PostModel::sendRecoveryCode($email);
        echo json_encode($response, http_response_code($response['status']));
    }


    /*Función para responder JSON*/
    public static function fncResponse($response, $error, $suffix)
    {
        if (!empty($response) && is_array($response) && isset($response[0])) {
            if (isset($response[0]->{"password_" . $suffix})) {
                unset($response[0]->{"password_" . $suffix});
            }
            $json = array(
                'status' => 200,
                'result' => $response
            );
        } else {
            $json = array(
                'status' => 400,
                'result' => $error ?? 'Error en la solicitud'
            );
        }

        echo json_encode($json, http_response_code($json['status']));
    }
}
