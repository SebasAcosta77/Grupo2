<?php 

require_once "connection.php";
require_once "get.model.php";

class PutModel
{
	/*=============================================
	Petición PUT para editar datos de forma dinámica
	=============================================*/
	static public function putData($table, $data, $id, $nameId)
	{
		/*=============================================
		Validar el ID
		=============================================*/
		$response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);

		if (empty($response)) {
			return null;
		}

		/*=============================================
		Actualizamos registros
		=============================================*/
		$set = "";

		// Construir la cláusula SET
		foreach ($data as $key => $value) {
			$set .= $key . " = :" . $key . ",";
		}

		// Eliminar la última coma
		$set = rtrim($set, ",");

		$sql = "UPDATE $table SET $set WHERE $nameId = :$nameId";

		$link = Connection::connect();
		$stmt = $link->prepare($sql);

		// Vincular los parámetros
		foreach ($data as $key => $value) {
			$stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
		}

		$stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return [
				"comment" => "The process was successful"
			];
		} else {
			return $link->errorInfo();
		}
	}
}
