<?php

class Connection
{
    /* Información de la BD */
    static public function infoDatabase()
    {
        return array(
            "database" => "tienda",
            "user" => "user_node",
            "pass" => "123456"
        );
    }

    // apikey
    static public function apikey()
    {
        return "MYAPI";
    }

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
}
?>
