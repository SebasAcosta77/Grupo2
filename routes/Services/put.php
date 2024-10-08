<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

<<<<<<< HEAD
if(isset($_GET["id"]) && isset($_GET["nameId"])){

	/*=============================================
	Capturamos los datos del formulario
	=============================================*/
	
	$data = array();
	parse_str(file_get_contents('php://input'), $data);
		
	/*=============================================
	Separar propiedades en un arreglo
	=============================================*/

	$columns = array();
		
	foreach (array_keys($data) as $key => $value) {

		array_push($columns, $value);
		
	}
=======
define('HTTP_OK', 200);
define('HTTP_BAD_REQUEST', 400);

if (isset($_GET["id"]) && isset($_GET["nameId"])) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que $data no esté vacío
    if (empty($data)) {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "Los datos de entrada están vacíos"
        ], http_response_code(HTTP_BAD_REQUEST));
        return;
    }

    $columns = array_keys($data);
    array_push($columns, $_GET["nameId"]);
    $columns = array_unique($columns);

    // Validar que los nombres de columna coincidan
    if (empty(Connection::getColumnsData($table, $columns))) {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "Los datos no coinciden"
        ], http_response_code(HTTP_BAD_REQUEST));
        return;
    }

    // Aquí iría la lógica para actualizar los datos usando PutController
    $response = PutController::putModel($table, $data, $_GET["id"], $_GET["nameId"]);
    
    if ($response) {
        echo json_encode([
            "status" => HTTP_OK,
            "result" => "Datos actualizados correctamente"
        ], http_response_code(HTTP_OK));
    } else {
        echo json_encode([
            "status" => HTTP_BAD_REQUEST,
            "result" => "No se pudieron actualizar los datos"
        ], http_response_code(HTTP_BAD_REQUEST));
    }
} else {
    echo json_encode([
        "status" => HTTP_BAD_REQUEST,
        "result" => "Faltan parámetros"
    ], http_response_code(HTTP_BAD_REQUEST));
}
>>>>>>> parent of 4914481 (SIIIIIII)

	array_push($columns, $_GET["nameId"]);

	$columns = array_unique($columns);

	/*=============================================
	Validar la tabla y las columnas
	=============================================*/

	if(empty(Connection::getColumnsData($table, $columns))){

		$json = array(
		 	'status' => 400,
		 	'results' => "Error: Fields in the form do not match the database"
		);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}

	if(isset($_GET["token"])){

		/*=============================================
		Peticion PUT para usuarios no autorizados
		=============================================*/

		if($_GET["token"] == "no" && isset($_GET["except"])){

			/*=============================================
			Validar la tabla y las columnas
			=============================================*/

			$columns = array($_GET["except"]);

			if(empty(Connection::getColumnsData($table, $columns))){

				$json = array(
				 	'status' => 400,
				 	'results' => "Error: Fields in the form do not match the database"
				);

				echo json_encode($json, http_response_code($json["status"]));

				return;

			}

			/*=============================================
			Solicitamos respuesta del controlador para crear datos en cualquier tabla
			=============================================*/		

			$response = new PutController();
			$response -> putData($table,$data,$_GET["id"],$_GET["nameId"]);
			
		/*=============================================
		Peticion PUT para usuarios autorizados
		=============================================*/

		}else{

			$tableToken = $_GET["table"] ?? "users";
			$suffix = $_GET["suffix"] ?? "user";

			$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

			/*=============================================
			Solicitamos respuesta del controlador para editar datos en cualquier tabla
			=============================================*/		

			if($validate == "ok"){
				
				$response = new PutController();
				$response -> putData($table,$data,$_GET["id"],$_GET["nameId"]);

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