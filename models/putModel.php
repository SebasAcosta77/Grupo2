<?php

class PutModel {
    static public function putModel($table, $data, $id, $nameId) {
        // Obtener datos existentes para validar la actualización
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);
        if (empty($response)) {
            return ['status' => 404, 'message' => 'Registro no encontrado'];
        }

        // Preparar los campos para la actualización
        $set = "";
        foreach ($data as $key => $value) {
            // Sanitizar el valor antes de usarlo en la consulta
            $set .= "$key = :$key, ";
        }

        // Eliminar la última coma
        $set = rtrim($set, ', ');

        // Preparar la consulta SQL para la actualización
        $sql = "UPDATE $table SET $set WHERE $nameId = :$nameId";
        
        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        // Vincular parámetros a la consulta
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR); // Cambiar a PARAM_STR si es necesario
        }
        $stmt->bindValue(":$nameId", $id, PDO::PARAM_STR);

        // Ejecutar la consulta y retornar el resultado
        if ($stmt->execute()) {
            return ['status' => 200, 'message' => 'Proceso exitoso'];
        } else {
            return ['status' => 500, 'message' => 'Error en la actualización: ' . implode(', ', $stmt->errorInfo())];
        }
    }
}
?>
