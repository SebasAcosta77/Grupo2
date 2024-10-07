<?php

require_once "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer() {
        $this->mailer->isSMTP();
        $this->mailer->Host       = getenv('SMTP_HOST'); // Usa una variable de entorno
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = getenv('SMTP_USER'); // Usa una variable de entorno
        $this->mailer->Password   = getenv('SMTP_PASSWORD'); // Usa una variable de entorno
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $this->mailer->Port       = 465; // Puerto SMTP
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->Timeout    = 30; // Timeout
        $this->mailer->setFrom(getenv('SMTP_FROM'), 'cambio clave jdc'); // Usa una variable de entorno
        $this->mailer->isHTML(true);
    }

    public function sendRecoveryCode($to, $code) {
        // Validar la dirección de correo electrónico
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Correo electrónico no válido.'];
        }
        
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = 'Código de Recuperación de Contraseña';
            
            $body = $this->getEmailTemplate($code);
            
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = "Tu código de recuperación es: $code. Este código expirará en 1 hora.";

            $this->mailer->send();
            return ['success' => true, 'message' => 'Correo enviado correctamente.'];
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$this->mailer->ErrorInfo}");
            return ['success' => false, 'message' => 'Error al enviar correo: ' . $this->mailer->ErrorInfo];
        }
    }

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
}

// Ejemplo de uso
$emailSender = new EmailSender();
$response = $emailSender->sendRecoveryCode('recipient@example.com', '123456');
echo $response['message'];
?>
