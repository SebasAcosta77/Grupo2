<?php

require_once "models/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PostModel
{
    /** Método para registrar usuarios */
    public static function postData($table, $data)
    {
        // Generar columnas y parámetros para la consulta SQL
        $columns = "";
        $paramns = "";

        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $paramns .= ":" . $key . ",";
        }

        // Quitar la última coma
        $columns = substr($columns, 0, -1);
        $paramns = substr($paramns, 0, -1);

        // Preparar la consulta SQL para la inserción
        $sql = "INSERT INTO $table ($columns) VALUES ($paramns)";
        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        // Vincular parámetros a la consulta
        foreach ($data as $key => $value) {
            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }

        // Ejecutar la consulta y retornar el resultado
        if ($stmt->execute()) {
            $response = array(
                "lastId" => $link->lastInsertId(), // Retorna el último ID insertado
                "comentario" => "Registro exitoso"
            );
            return $response;
        } else {
            // En caso de error, devolver información sobre el fallo
            return $stmt->errorInfo();
        }
    }

    /** Método para obtener un usuario por email */
    public static function getDataByEmail($table, $email, $suffix)
    {
        // Preparar la consulta SQL para obtener datos por email
        $sql = "SELECT * FROM $table WHERE email_" . $suffix . " = :email";
        $link = Connection::connect();
        $stmt = $link->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        // Ejecutar la consulta y retornar los datos como un objeto
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /** Método para actualizar el token o cualquier otro dato en la base de datos */
    public static function updateData($table, $data, $id, $suffix)
    {
        // Generar el conjunto de columnas para actualizar
        $set = "";
        foreach ($data as $key => $value) {
            $set .= $key . " = :" . $key . ",";
        }
        // Quitar la última coma
        $set = substr($set, 0, -1);

        // Preparar la consulta SQL para la actualización
        $sql = "UPDATE $table SET $set WHERE id_" . $suffix . " = :id";
        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        // Vincular parámetros a la consulta
        foreach ($data as $key => $value) {
            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        // Ejecutar la consulta y retornar el resultado
        if ($stmt->execute()) {
            $response = array(
                "comentario" => "el proceso fue satisfactorio"
            );
            return $response;
        } else {
            // En caso de error, devolver información sobre el fallo
            return $stmt->errorInfo();
        }
    }

    /** Método para enviar el código de recuperación al correo */
    public static function sendRecoveryCode($email)
    {
        $link = Connection::connect();

        // Generar un código aleatorio de 6 dígitos
        $codigo_recuperacion = sprintf('%06d', mt_rand(0, 999999));

        // Actualizar el código en la base de datos
        $sql = "UPDATE users SET codigo_recuperacion_user = :codigo WHERE email_user = :email";
        $stmt = $link->prepare($sql);
        $stmt->bindParam(':codigo', $codigo_recuperacion, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Enviar el correo con el código
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.hostinger.com'; // Cambia esto por tu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'jdavidmartinez@jdc.edu.co'; // Cambia esto por tu correo
                $mail->Password = '1036598858151103'; // Cambia esto por tu contraseña
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Destinatarios
                $mail->setFrom('jdavidmartinez@jdc.edu.co', 'Nombre del Remitente');
                $mail->addAddress($email);

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Código de Recuperación de Contraseña';
                $mail->Body    = 'Tu código de recuperación es: ' . $codigo_recuperacion;

                $mail->send();
                return ['status' => 200, 'message' => 'Código enviado exitosamente'];
            } catch (Exception $e) {
                return ['status' => 500, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo];
            }
        } else {
            return ['status' => 400, 'message' => 'No se pudo actualizar el código'];
        }
    }
}
