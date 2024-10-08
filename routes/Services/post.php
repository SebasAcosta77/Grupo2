<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if(isset($_POST)){

<<<<<<< HEAD
   
    
    
	/*=============================================
	Separar propiedades en un arreglo
	=============================================*/

	$columns = array();
	
	foreach (array_keys($_POST) as $key => $value) {

		array_push($columns, $value);
			
	}

	/*=============================================
	Validar la tabla y las columnas
	=============================================*/

	if(empty(Connection::getColumnsData($table, $columns))){

		$json = array(
		 	'status' => 400,
		 	'results' => "Error:Los campos del formulario no coinciden con la base de datos"
		 	
		 	
		);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}

	$response = new PostController();

	/*=============================================
	Peticion POST para registrar usuario
	=============================================*/	

	if(isset($_GET["register"]) && $_GET["register"] == true){

		$suffix = $_GET["suffix"] ?? "user";

		$response -> postRegister($table,$_POST,$suffix);

	/*=============================================
	Peticion POST para login de usuario
	=============================================*/	

	}else if(isset($_GET["login"]) && $_GET["login"] == true){

		$suffix = $_GET["suffix"] ?? "user";

		$response -> postLogin($table,$_POST,$suffix);

	}else{


		if(isset($_GET["token"])){

			/*=============================================
			Peticion POST para usuarios no autorizados
			=============================================*/

			if($_GET["token"] == "no" && isset($_GET["except"])){

				/*=============================================
				Validar la tabla y las columnas
				=============================================*/

				$columns = array($_GET["except"]);
                
				if(empty(Connection::getColumnsData($table, $columns))){

					$json = array(
					 	'status' => 400,
					 	'results' => "Error: Fields in the form do not match the token"
					);

					echo json_encode($json, http_response_code($json["status"]));

					return;

				}

				/*=============================================
				Solicitamos respuesta del controlador para crear datos en cualquier tabla
				=============================================*/		

				$response -> postData($table,$_POST);

			/*=============================================
			Peticion POST para usuarios autorizados
			=============================================*/

			}else{

				$tableToken = $_GET["table"] ?? "users";
				$suffix = $_GET["suffix"] ?? "user";

				$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

				/*=============================================
				Solicitamos respuesta del controlador para crear datos en cualquier tabla
				=============================================*/		

				if($validate == "ok"){
		
					$response -> postData($table,$_POST);

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

}
=======
define('HTTP_OK', 200);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_NOT_FOUND', 404);

/** Separar las propiedades del arreglo */
$columns = array_keys($_POST);

/** Validamos las tablas y las columnas */
if (empty(Connection::getColumnsData($table, $columns))) {
    echo json_encode([
        'status' => HTTP_BAD_REQUEST,
        'message' => "Los nombres de los campos de la base de datos no coinciden"
    ], http_response_code(HTTP_BAD_REQUEST));
    return;
}

$response = new PostController();

/** Manejo de registro y login */
if (isset($_GET["register"]) && $_GET["register"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
    return;
}

if (isset($_GET["login"]) && $_GET["login"] == true) {
    $sufix = $_GET["sufix"] ?? "user";
    $response->postAuthResponse($table, $_POST, $sufix);
    return;
}

/** Validar el token JWT */
$token = $_POST['token'] ?? null;
if ($token) {
    try {
        $decoded = JWT::decode($token, $secret_key, ['HS256']);
    } catch (\Firebase\JWT\ExpiredException $e) {
        echo json_encode([
            'status' => HTTP_UNAUTHORIZED,
            'message' => 'El token ha expirado'
        ], http_response_code(HTTP_UNAUTHORIZED));
        return;
    } catch (Exception $e) {
        echo json_encode([
            'status' => HTTP_UNAUTHORIZED,
            'message' => 'Token inválido'
        ], http_response_code(HTTP_UNAUTHORIZED));
        return;
    }
}

/** Manejo de recuperación de contraseña */
if (isset($_GET["recovery"]) && $_GET["recovery"] == true) {
    $email = $_POST["email_user"] ?? null;
    if ($email) {
        $response->postRecoveryResponse($email);
    } else {
        echo json_encode(['status' => HTTP_BAD_REQUEST, 'message' => 'Email es requerido'], http_response_code(HTTP_BAD_REQUEST));
    }
}
>>>>>>> parent of 4914481 (SIIIIIII)
