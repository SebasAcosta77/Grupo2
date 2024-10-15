<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once "Models/Connection.php";

class VerifyEmailController {


    
    private function getEmailTemplate($code) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Código de Recuperación</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; text-align: center; padding: 10px; }
                .content { padding: 20px; background-color: #f4f4f4; }
                .code { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; background-color: #e7e7e7; padding: 10px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Código de Recuperación</h1>
                </div>
                <div class='content'>
                    <p>Hola,</p>
                    <p>Has solicitado un código para recuperar tu contraseña. Aquí está tu código:</p>
                    <div class='code'>$code</div>
                    <p>Este código expirará en 1 hora.</p>
                    <p>Si no has solicitado este código, por favor ignora este correo.</p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no respondas a esta dirección.</p>
                    <p>© " . date('Y') . " crediapp. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    public function sendVerificationCode($email) {
        // 1. Verificar si el correo está en la base de datos
        $stmt = Connection::connect()->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Si el usuario no existe
            $json = array(
                "status" => 404,
                "results" => "Usuario no encontrado."
            );
            echo json_encode($json);
            http_response_code(404);
            return;
        }

        // 2. Generar código de verificación de 6 dígitos
        $verificationCode = rand(100000, 999999);

        // 3. Guardar el código y la expiración en la base de datos
        $token = $verificationCode;
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

        $updateStmt = Connection::connect()->prepare("UPDATE usuarios SET token_password = :token, expired_session = :expiration WHERE email = :email");
        $updateStmt->bindParam(":token", $token, PDO::PARAM_STR);
        $updateStmt->bindParam(":expiration", $expiration, PDO::PARAM_STR);
        $updateStmt->bindParam(":email", $email, PDO::PARAM_STR);
        $updateStmt->execute();

        // 4. Enviar el correo con PHPMailer
        $this->sendEmail($email, $verificationCode);

        // 5. Devolver respuesta exitosa
        $json = array(
            "status" => 200,
            "results" => "Código de verificación enviado correctamente."
        );
        echo json_encode($json);
        http_response_code(200);
    }

    // Método para enviar el correo
    private function sendEmail($email, $verificationCode) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jdc@tunjatienevoz.com';
            $mail->Password   = 'Jdc.email.2024';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->setFrom('jdc@tunjatienevoz.com', 'cambio clave jdc');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Código de verificación';

            $body = $this -> getEmailTemplate($verificationCode);

            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
        }
    }

    public function changePassword($email, $code, $newPassword) {
        // Conexión a la base de datos
        $db = Connection::connect();

        // Buscar el usuario por email
        $stmt = $db->prepare("SELECT token_password, expired_session, failed_attempts FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar si el usuario existe
        if (!$user) {
            return json_encode(["status" => 404, "message" => "Usuario no encontrado"]);
        }

        // Validar el número de intentos fallidos
        if ($user['failed_attempts'] >= 3) {
            $stmt = $db->prepare("UPDATE usuarios SET password = :password, failed_attempts = 0, token_password = NULL, expired_session = NULL WHERE email = :email");
            return json_encode(["status" => 403, "message" => "Has superado el número de intentos permitidos, genera un nuevo codigo"]);
        }

        // Validar si el código ha expirado
        if (strtotime($user['expired_session']) < time()) {
            return json_encode(["status" => 400, "message" => "El código ha expirado"]);
        }

        // Verificar si el código es correcto
        if ($user['token_password'] != $code) {
            // Incrementar el contador de intentos fallidos
            $db->prepare("UPDATE usuarios SET failed_attempts = failed_attempts + 1 WHERE email = :email")
               ->execute([":email" => $email]);

            return json_encode(["status" => 400, "message" => "Código incorrecto"]);
        }

        // Cambiar la contraseña si el código es correcto
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); // Encriptar la contraseña
        $stmt = $db->prepare("UPDATE usuarios SET password = :password, failed_attempts = 0, token_password = NULL, expired_session = NULL WHERE email = :email");
        $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return json_encode(["status" => 200, "message" => "Contraseña cambiada exitosamente"]);
        }

        return json_encode(["status" => 500, "message" => "Error al cambiar la contraseña"]);
    }
}