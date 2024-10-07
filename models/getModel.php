<?php

class GetModel
{
    /* Peticiones GET sin filtro */
    static public function getData($table, $select = '*', $orderBy = null, $orderMode = 'ASC', $startAt = 0, $endAt = 10)
{
    // Validar select
    $select = preg_replace('/[^a-zA-Z0-9_, ]/', '', $select); // Solo permite letras, números, comas y espacios
    $orderBy = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy); // Solo permite letras y números
    $orderMode = strtoupper($orderMode) === 'DESC' ? 'DESC' : 'ASC'; // Asegúrate de que sea ASC o DESC

    $sql = "SELECT $select FROM $table";
    $sql .= self::buildOrderLimit($orderBy, $orderMode, (int)$startAt, (int)$endAt); // Convertir a int para evitar inyecciones
    return self::executeQuery($sql);
}

static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
{
    $select = preg_replace('/[^a-zA-Z0-9_, ]/', '', $select);
    $linkTo = preg_replace('/[^a-zA-Z0-9_]/', '', $linkTo);
    
    $sql = "SELECT $select FROM $table WHERE $linkTo = :equalTo";
    $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);

    $stmt = Connection::connect()->prepare($sql);
    $stmt->bindParam(':equalTo', $equalTo);
    try {
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    } catch (PDOException $Exception) {
        error_log($Exception->getMessage());  // Registrar el error
        return null;
    }
}


    /* Peticiones GET entre tablas relacionadas */
    static public function getRelData($rel, $type, $select, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);

        $innerJoinText = self::buildInnerJoin($relArray, $typeArray);
        $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $relArray[0].id_$typeArray[0] = '$equalTo'";
        $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);
        return self::executeQuery($sql);
    }

    /* Peticiones GET entre tablas relaciones con filtro */
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);

        $innerJoinText = self::buildInnerJoin($relArray, $typeArray);
        $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo = '$equalTo'";
        $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);
        return self::executeQuery($sql);
    }

    /* Peticiones GET para búsqueda de datos */
    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        $sql = "SELECT $select FROM $table WHERE $linkTo LIKE '%$search%'";
        $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);
        return self::executeQuery($sql);
    }

    /* Peticiones GET para búsqueda entre tablas relacionadas */
    static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);

        $innerJoinText = self::buildInnerJoin($relArray, $typeArray);
        $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo LIKE '%$search%'";
        $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);
        return self::executeQuery($sql);
    }

    /* Peticiones GET para filtrar por rango */
    static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {
        $filter = "";
        if ($filterTo != null && $inTo != null) {
            $filter = " AND $filterTo IN ($inTo)";
        }

        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";
        $sql .= self::buildOrderLimit($orderBy, $orderMode, $startAt, $endAt);
        return self::executeQuery($sql);
    }

    private static function buildInnerJoin($relArray, $typeArray)
    {
        $innerJoinText = "";
        foreach ($relArray as $key => $value) {
            if ($key > 0) {
                $innerJoinText .= "INNER JOIN $value ON $relArray[0].id_$typeArray[$key]_$typeArray[0] = $value.id_$typeArray[$key] ";
            }
        }
        return $innerJoinText;
    }

    private static function buildOrderLimit($orderBy, $orderMode, $startAt, $endAt)
    {
        $sql = "";
        if ($orderBy != null && $orderMode != null) {
            $sql .= " ORDER BY $orderBy $orderMode";
        }
        if ($startAt != null && $endAt != null) {
            $sql .= " LIMIT $startAt, $endAt";
        }
        return $sql;
    }

    private static function executeQuery($sql)
    {
        error_log("Consulta SQL ejecutada: $sql"); // Para ver la consulta que se ejecuta
        $stmt = Connection::connect()->prepare($sql);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (PDOException $Exception) {
            error_log($Exception->getMessage());  // Registrar el error
            return null;
        }
    }
}
