<?php 

require_once "connection.php";
require_once "get.model.php";

class DeleteModel
{
	/*=============================================
	Petición Delete para eliminar datos de forma dinámica
	=============================================*/
	static public function deleteData($table, $id, $nameId)
	{
		/*=============================================
		Validar el ID
		=============================================*/
		$response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);
		
		if (empty($response)) {
			return null;
		}

		/*=============================================
		Eliminamos registros
		=============================================*/
		$sql = "DELETE FROM $table WHERE $nameId = :$nameId";
		$link = Connection::connect();
		$stmt = $link->prepare($sql);
		$stmt->bindParam(":$nameId", $id, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return [
				"comment" => "The process was successful"
			];
		} else {
			return $link->errorInfo();
		}
	}
}
