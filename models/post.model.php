<?php 

require_once "connection.php";

class PostModel
{
	/*=============================================
	Petición POST para crear datos de forma dinámica
	=============================================*/
	static public function postData($table, $data)
	{
		$columns = "";
		$params = "";

		// Construir las columnas y los parámetros
		foreach ($data as $key => $value) {
			$columns .= $key . ",";
			$params .= ":" . $key . ",";
		}

		// Eliminar la última coma
		$columns = rtrim($columns, ",");
		$params = rtrim($params, ",");

		$sql = "INSERT INTO $table ($columns) VALUES ($params)";

		$link = Connection::connect();
		$stmt = $link->prepare($sql);

		// Vincular los parámetros
		foreach ($data as $key => $value) {
			$stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
		}

		if ($stmt->execute()) {
			return [
				"lastId" => $link->lastInsertId(),
				"comment" => "The process was successful"
			];
		} else {
			return $link->errorInfo();
		}
	}
}
