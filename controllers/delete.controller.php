<?php

class DeleteController {
    /* Petición para eliminar datos */
    static public function deleteData($table, $id, $nameId) {
        $response = DeleteModel::deleteData($table, $id, $nameId);

        $return = new DeleteController();
        $return ->fncResponse($response);
    }

    /* Petición para eliminar datos con condiciones específicas */
    static public function deleteConditionalData($table, $conditions) {
        $response = DeleteModel::deleteConditionalData($table, $conditions);

        $return = new DeleteController();
        $return ->fncResponse($response);
    }

    public function fncResponse($response) {
        if ($response) {
            $json = array(
                'status' => 200,
                'result' => 'Eliminación exitosa'
            );
        } else {
            $json = array(
                'status' => 400,
                'result' => 'No se pudo eliminar el registro'
            );
        }

        echo json_encode($json, http_response_code($json["status"]));
    }
}

?>
