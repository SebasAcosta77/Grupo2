<?php

class DeleteModel {
    static public function deleteData($tabla, $id, $nameId) {
        // Verificar si el registro existe antes de eliminar
        $respnse = GetModel::getDataFilter($tabla, $nameId, $nameId, $id, null, null, null, null);
        if (empty($respnse)) {
            return [
                "status" => 404,
                "message" => "No se encontró el registro para eliminar."
            ];
        }

        $sql = "DELETE FROM $tabla WHERE $nameId = :$nameId";

        $link = Connection::connect();
        $stmp = $link->prepare($sql);
        $stmp->bindParam(':'.$nameId, $id, PDO::PARAM_STR);
        
        try {
            if ($stmp->execute()) {
                return [
                    "status" => 200,
                    "message" => "Proceso exitoso"
                ];
            } else {
                return [
                    "status" => 500,
                    "message" => "Error al eliminar el registro.",
                    "error" => $link->errorInfo()
                ];
            }
        } catch (Exception $e) {
            // Manejo de excepciones, se puede registrar el error
            error_log("Error en deleteData: " . $e->getMessage(), 3, "/var/log/app_errors.log");
            return [
                "status" => 500,
                "message" => "Error inesperado."
            ];
        }
    }
}
?>
