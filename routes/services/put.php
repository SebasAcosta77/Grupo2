<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

if (isset($_GET["id"]) && isset($_GET["nameId"])) {

	/*=============================================
    Capturamos los datos del formulario
    =============================================*/
	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	/*=============================================
    Separar propiedades en un arreglo
    =============================================*/
	$columns = array_unique(array_merge(array_keys($data), [$_GET["nameId"]]));

	/*=============================================
    Validar la tabla y las columnas
    =============================================*/
	if (empty(Connection::getColumnsData($table, $columns))) {
		$json = array(
			'status' => 400,
			'results' => "Error: Los campos en el formulario no coinciden con la base de datos"
		);

		echo json_encode($json);
		http_response_code($json["status"]);
		return;
	}

	if (isset($_GET["token"])) {
		$response = new PutController();

		/*=============================================
        Peticion PUT para usuarios no autorizados
        =============================================*/
		if ($_GET["token"] === "no" && isset($_GET["except"])) {
			/*=============================================
            Validar la tabla y las columnas
            =============================================*/
			$columns = array($_GET["except"]);

			if (empty(Connection::getColumnsData($table, $columns))) {
				$json = array(
					'status' => 400,
					'results' => "Error: Los campos en el formulario no coinciden con la base de datos"
				);

				echo json_encode($json);
				http_response_code($json["status"]);
				return;
			}

			/*=============================================
            Solicitar respuesta del controlador para crear datos en cualquier tabla
            =============================================*/
			$response->putData($table, $data, $_GET["id"], $_GET["nameId"]);

			/*=============================================
        Peticion PUT para usuarios autorizados
        =============================================*/
		} else {
			$tableToken = $_GET["table"] ?? "users";
			$suffix = $_GET["suffix"] ?? "user";

			$validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);

			/*=============================================
            Solicitar respuesta del controlador para editar datos en cualquier tabla
            =============================================*/
			if ($validate === "ok") {
				$response->putData($table, $data, $_GET["id"], $_GET["nameId"]);
			}

			/*=============================================
            Error cuando el token ha expirado
            =============================================*/
			if ($validate === "expired") {
				$json = array(
					'status' => 303,
					'results' => "Error: El token ha expirado"
				);

				echo json_encode($json);
				http_response_code($json["status"]);
				return;
			}

			/*=============================================
            Error cuando el token no coincide en BD
            =============================================*/
			if ($validate === "no-auth") {
				$json = array(
					'status' => 400,
					'results' => "Error: El usuario no está autorizado"
				);

				echo json_encode($json);
				http_response_code($json["status"]);
				return;
			}
		}

		/*=============================================
    Error cuando no envía token
    =============================================*/
	} else {
		$json = array(
			'status' => 400,
			'results' => "Error: Se requiere autorización"
		);

		echo json_encode($json);
		http_response_code($json["status"]);
		return;
	}
}
