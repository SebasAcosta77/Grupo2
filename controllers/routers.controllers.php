<?php

class RoutersController {
    // Ruta principal de nuestra base de datos
    public function index() {
        // Incluir el archivo de rutas
        if (file_exists("routes/routes.php")) {
            include "routes/routes.php";
        } else {
            // Manejo de errores si el archivo de rutas no existe
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'El archivo de rutas no fue encontrado'
            ]);
        }
    }
}
?>
