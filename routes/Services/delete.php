<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

<<<<<<< HEAD
if(isset($_GET["id"]) && isset($_GET["nameId"])){

	$columns = array($_GET["nameId"]);
=======
function jsonResponse($status, $result) {
    http_response_code($status);
    echo json_encode(array(
        "status" => $status,
        "result" => $result
    ));
}

if (!empty($_GET["table"]) && !empty($_GET["id"]) && !empty($_GET["nameId"])) {
    $table = $_GET["table"];
    $id = $_GET["id"];
    $nameId = $_GET["nameId"];

    // Check if the column exists in the table
    $columns = array($nameId);
    if (empty(Connection::getColumnsData($table, $columns))) {
        jsonResponse(400, "Los datos no coinciden");
        return;
    }
>>>>>>> parent of 4914481 (SIIIIIII)

	/*=============================================
	Validar la tabla y las columnas
	=============================================*/

<<<<<<< HEAD
	if(empty(Connection::getColumnsData($table, $columns))){

		$json = array(
		 	'status' => 400,
		 	'results' => "Error: Fields in the form do not match the database"
		);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}

	/*=============================================
	Peticion DELETE para usuarios autorizados
	=============================================*/

	if(isset($_GET["token"])){

		$tableToken = $_GET["table"] ?? "users";
		$suffix = $_GET["suffix"] ?? "user";

		$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

		/*=============================================
		Solicitamos respuesta del controlador para eliminar datos en cualquier tabla
		=============================================*/	
			
		if($validate == "ok"){
	
			$response = new DeleteController();
			$response -> deleteData($table,$_GET["id"],$_GET["nameId"]);

		}

		/*=============================================
		Error cuando el token ha expirado
		=============================================*/	

		if($validate == "expired"){

			$json = array(
			 	'status' => 303,
			 	'results' => "Error: The token has expired"
			);

			echo json_encode($json, http_response_code($json["status"]));

			return;

		}

		/*=============================================
		Error cuando el token no coincide en BD
		=============================================*/	

		if($validate == "no-auth"){

			$json = array(
			 	'status' => 400,
			 	'results' => "Error: The user is not authorized"
			);

			echo json_encode($json, http_response_code($json["status"]));

			return;

		}

	/*=============================================
	Error cuando no envía token
	=============================================*/	

	}else{

		$json = array(
		 	'status' => 400,
		 	'results' => "Error: Authorization required"
		);

		echo json_encode($json, http_response_code($json["status"]));

		return;	

	}	

}

=======
    if ($response) {
        jsonResponse(200, "El registro ha sido eliminado");
    } else {
        jsonResponse(400, "No se pudo eliminar el registro");
    }
} else {
    jsonResponse(400, "Faltan parámetros");
}

/** Tarea: Validar el token si coincide o no con el almacenado en la base de datos */

?>
>>>>>>> parent of 4914481 (SIIIIIII)
