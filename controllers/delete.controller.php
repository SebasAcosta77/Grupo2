<?php 

require_once "models/delete.model.php";

class DeleteController {

    /*=============================================
    Petición Delete para eliminar datos
    =============================================*/
    public static function deleteData($table, $id, $nameId) {

        // Validación básica de los parámetros
        if (empty($table) || empty($id) || empty($nameId)) {
            return self::sendResponse(400, "Bad Request: Missing parameters.");
        }

        try {
            // Intentar eliminar los datos utilizando el modelo
            $response = DeleteModel::deleteData($table, $id, $nameId);

            if ($response) {
                // Enviar respuesta con éxito si se elimina correctamente
                return self::sendResponse(200, $response);
            } else {
                // Enviar respuesta si no se encuentran datos
                return self::sendResponse(404, "Not Found: No data to delete.");
            }
        } catch (Exception $e) {
            // Manejo de excepciones, enviando mensaje de error
            return self::sendResponse(500, "Internal Server Error: " . $e->getMessage());
        }
    }

    /*=============================================
    Método para enviar respuestas del controlador
    =============================================*/
    private static function sendResponse($statusCode, $message) {

        // Crear la respuesta JSON con el código de estado y los resultados
        $response = array(
            'status' => $statusCode,
            'results' => $message
        );

        // Establecer el código de respuesta HTTP
        http_response_code($statusCode);

        // Enviar la respuesta en formato JSON
        echo json_encode($response);
        exit; // Detener ejecución para evitar más salida de datos
    }
}
