<?php

require_once "get.model.php";

class Connection
{
<<<<<<< HEAD

	/*=============================================
	Información de la base de datos
	=============================================*/

	static public function infoDatabase()
	{

		$infoDB = array(

			"database" => 'u145597152_grupodos',
            "user" => 'u145597152_ugrupodos',
            "pass" =>'V6w$eW/y&@' 
=======
    /* Información de la BD */
    static public function infoDatabase()
    {
        return array(
            "database" => "u145597152_grupodos",
            "user" => "u145597152_ugrupodos",
            "pass" => 'V6w$eW/y&@'
        );
    }
>>>>>>> parent of 4914481 (SIIIIIII)

		);

<<<<<<< HEAD
		return $infoDB;
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

		$tables = ["usuario", "libros", "usuario_libro"];

		return $tables;
	}

	/*=============================================
	Conexión a la base de datos
	=============================================*/

	static public function connect()
	{


		try {

			$link = new PDO(
				"mysql:host=localhost;dbname=" . Connection::infoDatabase()["database"],
				Connection::infoDatabase()["user"],
				Connection::infoDatabase()["pass"]
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

		$database = Connection::infoDatabase()["database"];

		/*=============================================
		Traer todas las columnas de una tabla
		=============================================*/

		$validate = Connection::connect()
			->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
			->fetchAll(PDO::FETCH_OBJ);

		/*=============================================
		Validamos existencia de la tabla
		=============================================*/

		if (empty($validate)) {

			return null;
		} else {

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

			foreach ($validate as $key => $value) {

				$sum += in_array($value->item, $columns);
			}



			return $sum == count($columns) ? $validate : null;
		}
	}

	/*=============================================
	Generar Token de Autenticación
	=============================================*/

	static public function jwt($id, $email)
	{

		$time = time();

		$token = array(

			"iat" =>  $time, //Tiempo en que inicia el token
			"exp" => $time + (60 * 60 * 24), // Tiempo en que expirará el token (1 día)
			"data" => [

				"id" => $id,
				"email" => $email
			]

		);

		return $token;
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
=======
    static public function publicAccess()
    {
        // Las tablas que queremos que sean públicas
        return ["libros", "usuario", "usuario_libro"];
    }

    static public function connect()
    {
        try {
            $link = new PDO(
                "mysql:host=localhost;dbname=" . self::infoDatabase()["database"],
                self::infoDatabase()["user"],
                self::infoDatabase()["pass"]
            );
            $link->exec("set names utf8mb4"); // Mejor manejo de caracteres
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores
        } catch (PDOException $e) {
            // Considera registrar el error en un archivo de log
            error_log("Error de conexión: " . $e->getMessage(), 3, "/var/log/app_errors.log"); // Cambia la ruta según sea necesario
            die("Error: No se pudo conectar a la base de datos."); // Mensaje más genérico
        }
        return $link;
    }

    /* Validar existencia de tablas y columnas en la base de datos */
    static public function getColumnsData($table, $columns)
    {
        $database = self::infoDatabase()["database"];

        try {
            $query = self::connect()->query(
                "SELECT column_name FROM information_schema.columns 
                WHERE table_schema = '$database' AND table_name = '$table'"
            );

            $result = $query->fetchAll(PDO::FETCH_COLUMN);

            if (empty($result)) {
                return null; // La tabla no existe
            }

            if (empty($columns) || !is_array($columns)) {
                return null; // Retornar null si $columns está vacío o no es un array
            }

            if ($columns[0] == "*") {
                return $result; // Retornar todas las columnas
            }

            foreach ($columns as $column) {
                if (!in_array($column, $result)) {
                    return null; // La columna no existe en la tabla
                }
            }

            return $result; // Retornar las columnas si todo está bien
        } catch (Exception $e) {
            error_log("Error al obtener columnas: " . $e->getMessage(), 3, "/var/log/app_errors.log");
            return null; // Manejo de errores
        }
    }

    /* Método para generar token JWT */
    public static function jwt($id, $email)
    {
        $key = "a2%4ndjle$%&ashbdajs-5avs"; // Considera manejar esta clave de forma segura
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Tiempo de expiración: 1 hora
        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'id' => $id,
            'email' => $email
        );

        return $payload; // Devolver el payload para codificarlo más adelante
    }
>>>>>>> parent of 4914481 (SIIIIIII)
}
?>
