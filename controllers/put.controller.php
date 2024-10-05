<?php

class PutController {
    /* Petición para actualizar datos */
    static public function putData($table, $data, $id, $nameId) {
        $response = PutModel::putData($table, $data, $id, $nameId);
        self::fncResponse($response);
    }

    /* Petición para actualizar datos de un usuario */
    static public function putUser($table, $data, $id, $suffix) {
        if (isset($data["password_" . $suffix]) && ($data["password_" . $suffix] != null)) {
            // Encriptar la contraseña si está presente
            $crypt = password_hash($data["password_" . $suffix], PASSWORD_BCRYPT);
            $data["password_" . $suffix] = $crypt;
        }
        $response = PutModel::putData($table, $data, $id, "id_" . $suffix);
        self::fncResponse($response);
    }

    /* Petición para actualizar datos con condiciones específicas */
    public static function putConditionalData($table, $data, $conditions) {
        // Conectar a la base de datos
        $connection = Connection::connect();

        // Crear la parte de la consulta SQL
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = :$column";
        }
        $setString = implode(", ", $setClause);

        // Crear la parte de condiciones
        $whereClause = [];
        foreach ($conditions as $column => $value) {
            $whereClause[] = "$column = :where_$column";
        }
        $whereString = implode(" AND ", $whereClause);

        // Crear la consulta SQL
        $sql = "UPDATE $table SET $setString WHERE $whereString";

        // Preparar la declaración
        $stmt = $connection->prepare($sql);

        // Vincular los parámetros
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":where_$column", $value);
        }

        // Ejecutar la declaración
        if ($stmt->execute()) {
            return ['comentario' => 'El proceso fue satisfactorio'];
        } else {
            // Manejar el error de manera adecuada
            return ['comentario' => 'Error al actualizar los datos'];
        }
    }

    /* Función para responder con JSON */
    public static function fncResponse($response) {
        header('Content-Type: application/json'); // Establecer tipo de contenido JSON
        if (!empty($response)) {
            $json = array(
                'status' => 200,
                'result' => $response
            );
            http_response_code(200);
        } else {
            $json = array(
                'status' => 400,
                'result' => 'No se pudo actualizar los datos'
            );
            http_response_code(400);
        }
        echo json_encode($json);
    }
}
?>
