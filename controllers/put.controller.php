<?php 

<<<<<<< HEAD
require_once "models/put.model.php";

class PutController{
=======
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
>>>>>>> parent of 4914481 (SIIIIIII)

	/*=============================================
	Peticion Put para editar datos
	=============================================*/

	static public function putData($table, $data, $id, $nameId){

		$response = PutModel::putData($table, $data, $id, $nameId);
		
		$return = new PutController();
		$return -> fncResponse($response);

	}

	/*=============================================
	Respuestas del controlador
	=============================================*/

	public function fncResponse($response){

<<<<<<< HEAD
		if(!empty($response)){

			$json = array(

				'status' => 200,
				'results' => $response

			);

		}else{

			$json = array(

				'status' => 404,
				'results' => 'Not Found',
				'method' => 'put'

			);

		}

		echo json_encode($json, http_response_code($json["status"]));

	}

}
=======
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
>>>>>>> parent of 4914481 (SIIIIIII)
