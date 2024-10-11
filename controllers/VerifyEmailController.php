<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "Models/Connection.php";

class VerifycorreoUsuarioController {
    
    // Método para obtener la plantilla del correo
    private function getcorreoUsuarioTemplate($code) {
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

    // Método para enviar el código de verificación
    public function sendVerificationCode($correoUsuario) {
        // 1. Verificar si el correo está en la base de datos
        $stmt = Connection::connect()->prepare("SELECT * FROM usuario WHERE correoUsuario = :correoUsuario");
        $stmt->bindParam(":correoUsuario", $correoUsuario, PDO::PARAM_STR);
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
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

        $updateStmt = Connection::connect()->prepare("UPDATE usuario SET token_password = :token, expired_session = :expiration WHERE correoUsuario = :correoUsuario");
        $updateStmt->bindParam(":token", $verificationCode, PDO::PARAM_STR);
        $updateStmt->bindParam(":expiration", $expiration, PDO::PARAM_STR);
        $updateStmt->bindParam(":correoUsuario", $correoUsuario, PDO::PARAM_STR);
        $updateStmt->execute();

        // 4. Enviar el correo con PHPMailer
        if ($this->sendcorreoUsuario($correoUsuario, $verificationCode)) {
            // 5. Devolver respuesta exitosa
            $json = array(
                "status" => 200,
                "results" => "Código de verificación enviado correctamente."
            );
            echo json_encode($json);
            http_response_code(200);
        } else {
            // Si el envío del correo falla
            $json = array(
                "status" => 500,
                "results" => "Error al enviar el correo de verificación."
            );
            echo json_encode($json);
            http_response_code(500);
        }
    }

    // Método para enviar el correo
    private function sendcorreoUsuario($correoUsuario, $verificationCode) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jdc@tunjatienevoz.com';
            $mail->Password   = 'Jdc.correoUsuario.2024';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->setFrom('jdc@tunjatienevoz.com', 'cambio clave jdc');
            $mail->addAddress($correoUsuario);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Código de verificación';
            $mail->Body = $this->getcorreoUsuarioTemplate($verificationCode);

            // Enviar el correo
            $mail->send();
            return true; // Retorna true si el envío fue exitoso
        } catch (Exception $e) {
            // Manejar el error del envío
            error_log("El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}"); // Registra el error
            return false; // Retorna false si hubo un error
        }
    }
}
