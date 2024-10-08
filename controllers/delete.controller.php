<?php 

<<<<<<< HEAD
require_once "models/delete.model.php";

class DeleteController{

	/*=============================================
	Peticion Delete para eliminar datos
	=============================================*/

	static public function deleteData($table, $id, $nameId){

		$response = DeleteModel::deleteData($table, $id, $nameId);
		
		$return = new DeleteController();
		$return -> fncResponse($response);

	}
=======
class DeleteController {
    /* Petición para eliminar datos */
    static public function deleteData($table, $id, $nameId) {
        if (empty($id)) {
            return self::fncResponse(null, 400, 'ID no puede estar vacío.');
        }
        
        $response = DeleteModel::deleteData($table, $id, $nameId);
        return self::fncResponse($response);
    }

    /* Petición para eliminar datos con condiciones específicas */
    static public function deleteConditionalData($table, $conditions) {
        $response = DeleteModel::deleteConditionalData($table, $conditions);
        return self::fncResponse($response);
    }

    public static function fncResponse($response, $status = 200, $message = null) {
        if ($response) {
            $json = array(
                'status' => $status,
                'result' => 'Eliminación exitosa'
            );
        } else {
            $json = array(
                'status' => $status,
                'result' => $message ?? 'No se pudo eliminar el registro'
            );
        }

        http_response_code($json["status"]); // Establecer el código de estado HTTP
        echo json_encode($json);
    }
}
>>>>>>> parent of 4914481 (SIIIIIII)

	/*=============================================
	Respuestas del controlador
	=============================================*/

	public function fncResponse($response){

		if(!empty($response)){

			$json = array(

				'status' => 200,
				'results' => $response

			);

		}else{

			$json = array(

				'status' => 404,
				'results' => 'Not Found',
				'method' => 'delete'

			);

		}

		echo json_encode($json, http_response_code($json["status"]));

	}

}