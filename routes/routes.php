<?php

require_once "models/connection.php";
require_once "controllers/get.controller.php";

$routesArray = array_filter(explode("/", $_SERVER['REQUEST_URI']));

<<<<<<< HEAD
/*=============================================
Cuando no se hace ninguna petici®Æn a la API
=============================================*/

if(count($routesArray) == 0){

	$json = array(

		'status' => 404,
		'results' => 'Not Found'

	);

	echo json_encode($json, http_response_code($json["status"]));

	return;

}

/*=============================================
Cuando si se hace una petici®Æn a la API
=============================================*/

if(count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])){

	$table = explode("?", $routesArray[1])[0];

	/*=============================================
	Validar llave secreta
	=============================================*/

	/**if(!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] != Connection::apikey()){

		if($table!='relations'&&in_array($table, Connection::publicAccess()) == 0){
	
			$json = array(
		
				'status' => 400,
				"results" => "You are not authorized to make this request"
				
			);

			echo json_encode($json, http_response_code($json["status"]));

			return;

		}else{

			/*=============================================
			Acceso p®≤blico
			=============================================/
			
	    	$response = new GetController();
			$response -> getData($table, "*",null,null,null,null);
            
			return;
		}
	
	}**/
	

	/*=============================================
	Peticiones GET
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "GET"){

		include "services/get.php";

	}

	/*=============================================
	Peticiones POST
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "POST"){

		include "services/post.php";

	}

	/*=============================================
	Peticiones PUT
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "PUT"){

		include "services/put.php";

	}

	/*=============================================
	Peticiones DELETE
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "DELETE"){

		include "services/delete.php";

	}

}

=======
// Validamos si se hace una petici√≥n a la API
if (count($routesArray) < 2) {
    echo json_encode(["status" => 404, "results" => "Not Found"], http_response_code(404));
    return;
}

$table = explode("?", $routesArray[1])[0]; // Obtener el nombre de la tabla
$secret_key = Connection::apikey(); // Obtener la clave secreta
$headers = apache_request_headers(); // Obtener los encabezados

// Funci√≥n para manejar las solicitudes
function handleRequest($method, $table) {
    switch ($method) {
        case "GET":
            include "Services/get.php";
            break;
        case "POST":
            include "Services/post.php";
            break;
        case "PUT":
            include "Services/put.php";
            break;
        case "DELETE":
            include "Services/delete.php";
            break;
        default:
            echo json_encode(["status" => 405, "results" => "Method Not Allowed"], http_response_code(405));
            break;
    }
}

// Validamos el acceso p√∫blico o privado
if (in_array($table, Connection::publicAccess())) {
    $response = new GetController();
    $response->getData($table, "*", null, null, null, null);

    handleRequest($_SERVER['REQUEST_METHOD'], $table);
} else {
    if (!isset($headers['Authorization']) || $headers['Authorization'] !== $secret_key) {
        echo json_encode(["status" => 401, "results" => "Unauthorized"], http_response_code(401));
        return;
    } else {
        handleRequest($_SERVER['REQUEST_METHOD'], $table);
    }
}
>>>>>>> parent of 4914481 (SIIIIIII)
