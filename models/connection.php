<?php

require_once "get.model.php";

class Connection
{
	/*=============================================
	Información de la base de datos
	=============================================*/
	static public function infoDatabase()
	{
		return [
			"database" => 'u145597152_grupodos',
			"user" => 'u145597152_ugrupodos',
			"pass" => 'V6w$eW/y&@'
		];
	}

	/*=============================================
	APIKEY
	=============================================*/
	static public function apikey()
	{
		return "c5LTA6WPbMwHhEabYu77nN9cn4VcMj";
	}

	/*=============================================
	Acceso público
	=============================================*/
	static public function publicAccess()
	{
		return ["usuario", "libros", "usuario_libro"];
	}

	/*=============================================
	Conexión a la base de datos
	=============================================*/
	static public function connect()
	{
		try {
			$link = new PDO(
				"mysql:host=localhost;dbname=" . self::infoDatabase()["database"],
				self::infoDatabase()["user"],
				self::infoDatabase()["pass"]
			);
			$link->exec("set names utf8");
		} catch (PDOException $e) {
			die("Error: " . $e->getMessage());
		}

		return $link;
	}

	/*=============================================
	Validar existencia de una tabla en la bd
	=============================================*/
	static public function getColumnsData($table, $columns)
	{
		/*=============================================
		Traer el nombre de la base de datos
		=============================================*/
		$database = self::infoDatabase()["database"];

		/*=============================================
		Traer todas las columnas de una tabla
		=============================================*/
		$validate = self::connect()
			->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
			->fetchAll(PDO::FETCH_OBJ);

		/*=============================================
		Validamos existencia de la tabla
		=============================================*/
		if (empty($validate)) {
			return null;
		}

		/*=============================================
		Ajuste de selección de columnas globales
		=============================================*/
		if ($columns[0] == "*") {
			array_shift($columns);
		}

		/*=============================================
		Validamos existencia de columnas
		=============================================*/
		$sum = 0;
		foreach ($validate as $value) {
			$sum += in_array($value->item, $columns);
		}

		return $sum == count($columns) ? $validate : null;
	}

	/*=============================================
	Generar Token de Autenticación
	=============================================*/
	static public function jwt($id, $email)
	{
		$time = time();
		return [
			"iat" => $time, // Tiempo en que inicia el token
			"exp" => $time + (60 * 60 * 24), // Tiempo en que expirará el token (1 día)
			"data" => [
				"id" => $id,
				"email" => $email
			]
		];
	}

	/*=============================================
	Validar el token de seguridad
	=============================================*/
	static public function tokenValidate($token, $table, $suffix)
	{
		/*=============================================
		Traemos el usuario de acuerdo al token
		=============================================*/
		$user = GetModel::getDataFilter($table, "token_exp_" . $suffix, "token_" . $suffix, $token, null, null, null, null);

		if (!empty($user)) {
			/*=============================================
			Validamos que el token no haya expirado
			=============================================*/
			$time = time();
			if ($time < $user[0]->{"token_exp_" . $suffix}) {
				return "ok";
			} else {
				return "expired";
			}
		} else {
			return "no-auth";
		}
	}
}
