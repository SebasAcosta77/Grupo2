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
        return ["productos", "users"];
    }

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

    /* Validar existencia de tablas y columnas en la base de datos */
static public function getColumnsData($table, $columns)
{
    // Traer el nombre de la base de datos
    $database = Connection::infoDatabase()["database"];

    // Obtener todas las columnas de la tabla en la base de datos
    $query = Connection::connect()->query(
        "SELECT column_name FROM information_schema.columns 
        WHERE table_schema = '$database' AND table_name = '$table'"
    );

    $result = $query->fetchAll(PDO::FETCH_COLUMN);

    // Validar la existencia de la tabla (si no tiene columnas, no existe)
    if (empty($result)) {
        return null; // La tabla no existe
    }

    // Validar que $columns no esté vacío
    if (empty($columns) || !is_array($columns)) {
        return null; // Retornar null si $columns está vacío o no es un array
    }

    // Si se solicitó "*", significa que no importa la validación de columnas
    if ($columns[0] == "*") {
        return $result; // Retornar todas las columnas
    }

    // Validar que las columnas solicitadas existan en la tabla
    foreach ($columns as $column) {
        if (!in_array($column, $result)) {
            return null; // La columna no existe en la tabla
        }
    }

    return $result; // Retornar las columnas si todo está bien
}


    /* Método para generar token JWT */
    public static function jwt($id, $email)
    {
        $key = "a2%4ndjle$%&ashbdajs-5avs"; // Clave secreta para firmar el token
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Tiempo de expiración: 1 hora
        $payload = array(
            'iat' => $issuedAt, // Tiempo de emisión
            'exp' => $expirationTime, // Tiempo de expiración
            'id' => $id, // ID del usuario
            'email' => $email // Email del usuario
        );

        return $payload; // Devolver el payload para codificarlo más adelante
    }
}
