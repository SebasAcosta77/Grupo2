<?php

class PutModel {
    /* Método para actualizar datos en la base de datos */
    public static function putData($table, $data, $id, $nameId) {
        // Conectar a la base de datos
        $connection = Connection::connect();

        // Crear la parte de la consulta SQL
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = :$column";
        }
        $setString = implode(", ", $setClause);

        // Crear la consulta SQL
        $sql = "UPDATE $table SET $setString WHERE $nameId = :id";

        // Preparar la declaración
        $stmt = $connection->prepare($sql);

        // Vincular los parámetros
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        $stmt->bindValue(":id", $id);

        // Ejecutar la declaración
        if ($stmt->execute()) {
            return ['comentario' => 'El proceso fue satisfactorio'];
        } else {
            // Manejar el error de manera adecuada
            return ['comentario' => 'Error al actualizar los datos: ' . implode(", ", $stmt->errorInfo())];
        }
    }
}
?>
