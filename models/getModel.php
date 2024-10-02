<?php
class GetModel
{
    /** peticiones get sin filtro */
    static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
    {
        // Conexión a la base de datos y validar la existencia de la tabla y columnas
        $selectArray = explode(",", $select);
        if (empty(Connection::getColumnsData($table, $selectArray))) {
            return null;
        }

        // Consulta base sin ordenar ni limitar
        $sql = "SELECT $select FROM $table";

        // Consultar para ordenar datos sin limitar
        if ($orderBy !== null && $orderMode !== null && $startAt === null && $endAt === null) {
            $sql .= " ORDER BY $orderBy $orderMode";
        }

        // Consultar para limitar los datos sin ordenar
        if ($orderBy === null && $orderMode === null && $startAt !== null && $endAt !== null) {
            $sql .= " LIMIT $startAt, $endAt";
        }

        // Consultar para ordenar y limitar los datos
        if ($orderBy !== null && $orderMode !== null && $startAt !== null && $endAt !== null) {
            $sql .= " ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);
        try {
            $stmt->execute();
        } catch (PDOException $exception) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }




    /* Peticiones get con filtro */
    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        // Validamos la existencia de la tabla y la columna
        $linkToArray = explode(",", $linkTo);
        $selectArray = explode(",", $select);
        $equalToArray = is_array($equalTo) ? $equalTo : [$equalTo]; // Aseguramos que equalTo sea un array

        // Verificación de la existencia de columnas
        if (empty(Connection::getColumnsData($table, $selectArray))) {
            return null;
        }
        /*   echo $table; */

        // Construcción de la consulta SQL con los filtros
        $sql = "SELECT $select FROM $table WHERE ";
        $conditions = [];

        foreach ($linkToArray as $index => $column) {
            $conditions[] = "$column = :$column";
        }

        // Unimos las condiciones con AND
        $sql .= implode(" AND ", $conditions);

        //Consultar para ordenar sin limitar
        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
            $sql .= " ORDER BY $orderBy $orderMode";  // Línea corregida: Variable "orderBy" mal escrita
        }

        //Consultar para ordenar y limitar
        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
            $sql .= " ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";  // Línea corregida: Variable "orderBy" mal escrita
        }

        //Limitar datos sin ordenar
        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
            $sql .= " LIMIT $startAt, $endAt";
        }

        // Depuración: Imprimir la consulta SQL generada
        /*  echo "Consulta SQL: " . $sql . "<br>";
 */
        // Preparación de la consulta
        $stmt = Connection::connect()->prepare($sql);

        // Vinculación de los valores a los parámetros
        foreach ($linkToArray as $index => $column) {
            $value = isset($equalToArray[$index]) ? $equalToArray[$index] : null;
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam(":$column", $value, $paramType);
        }

        try {
            $stmt->execute();
        } catch (PDOException $exception) {
            echo "Error en la consulta: " . $exception->getMessage();
            return null;
        }

        // Devuelve los resultados obtenidos
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    static public function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt)
    {
        //Validamos la existencia de la relación y la columna
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if ($relArray) {
            foreach ($relArray as $key => $value) {
                if (empty(Connection::getColumnsData($value, ["*"]))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText .= " INNER JOIN $value ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . "=" . $value . ".id_" . $typeArray[$key];  // Línea corregida: Faltaba espacio entre "INNER JOIN" y "$value"
                }
            }

            //Consulta base
            $sql = "SELECT $select FROM $relArray[0] $innerJoinText";

            //Consultar para ordenar sin limitar
            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
                $sql .= " ORDER BY $orderBy $orderMode";  // Línea corregida: Variable "orderBy" mal escrita
            }

            //Consultar para ordenar y limitar
            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
                $sql .= " ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";  // Línea corregida: Variable "orderBy" mal escrita
            }

            //Limitar datos sin ordenar
            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
                $sql .= " LIMIT $startAt, $endAt";
            }

            $stmt = Connection::connect()->prepare($sql);
            try {
                $stmt->execute();
            } catch (PDOException $exception) {
                return null;
            }
            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }

    //Peticiones get entre tablas relacionadas con filtro 
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderderBy, $orderMode, $startAt, $endAt)
    {
        //Validamos la existencia de la relacion y la columna
        $linkToArray = explode(",", $linkTo);
        $selectArray = explode(",", $select);
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $equalTo = explode(",", $equalTo);
        $innerJoinText = "";

        if ($linkToArray) {
            foreach ($linkToArray as $key => $value) {
                //Validamos la existencia de la columna y la tabla
                if (empty(Connection::getColumnsData($value, ["*"]))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "" . $typeArray[0] . " = " . $value . ".id" . $typeArray[$key] . " ";
                }
            }

            // sin ordenar y limitar datos
            $sql = "SELECT $select FROM  $relArray[0] $innerJoinText  WHERE $linkToArray[0] = :$linkToArray $innerJoinText";

            // ordenar datos sin limitar
            if ($orderderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
                $sql .= " ORDER BY $orderderBy $orderMode";
            }
            // ordenar y limitar datos
            if ($orderderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
                $sql .= " ORDER BY $orderderBy $orderMode LIMIT $startAt, $endAt";
            }
            // limitar datos sin ordenar
            if ($orderderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
                $sql .= " LIMIT $startAt, $endAt";
            }

            $smtp = Connection::connect()->prepare($sql);
            try {
                $smtp->execute();
            } catch (PDOException $exception) {
                return null;
            }
            return $smtp->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }

    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {

        /*=============================================
		Validar existencia de la tabla y de las columnas
		=============================================*/

        $linkToArray = explode(",", $linkTo);
        $selectArray = explode(",", $select);

        foreach ($linkToArray  as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        if (empty(Connection::getColumnsData($table, $selectArray))) {

            return null;
        }

        $searchArray = explode(",", $search);
        $linkToText = "";

        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {

                if ($key > 0) {

                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }


        /*=============================================
		Sin ordenar y sin limitar datos
		=============================================*/

        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";

        /*=============================================
		Ordenar datos sin limites
		=============================================*/

        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
        }
        /*=============================================
		Ordenar y limitar datos
		=============================================*/

        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        /*=============================================
		Limitar datos sin ordenar
		=============================================*/

        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        foreach ($linkToArray as $key => $value) {

            if ($key > 0) {

                $stmt->bindParam(":" . $value, $searchArray[$key], PDO::PARAM_STR);
            }
        }

        try {

            $stmt->execute();
        } catch (PDOException $Exception) {

            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }


    /*=============================================
	Peticiones GET para el buscador entre tablas relacionadas
	=============================================*/

    static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {


        /*=============================================
		Organizamos los filtros
		=============================================*/
        $linkToArray = explode(",", $linkTo);
        $searchArray = explode(",", $search);
        $linkToText = "";

        if (count($linkToArray) > 1) {

            foreach ($linkToArray as $key => $value) {

                if ($key > 0) {

                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }

        /*=============================================
		Organizamos las relaciones
		=============================================*/

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if (count($relArray) > 1) {

            foreach ($relArray as $key => $value) {

                /*=============================================
				Validar existencia de la tabla
				=============================================*/

                if (empty(Connection::getColumnsData($value, ["*"]))) {

                    return null;
                }

                if ($key > 0) {

                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "" . $typeArray[0] . " = " . $value . ".id" . $typeArray[$key] . " ";
                }
            }


            /*=============================================
			Sin ordenar y sin limitar datos
			=============================================*/

            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";

            /*=============================================
			Ordenar datos sin limites
			=============================================*/

            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
            }

            /*=============================================
			Ordenar y limitar datos
			=============================================*/

            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
            }
            /*=============================================
			Limitar datos sin ordenar
			=============================================*/

            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText LIMIT $startAt, $endAt";
            }

            $stmt = Connection::connect()->prepare($sql);

            foreach ($linkToArray as $key => $value) {

                if ($key > 0) {

                    $stmt->bindParam(":" . $value, $searchArray[$key], PDO::PARAM_STR);
                }
            }

            try {

                $stmt->execute();
            } catch (PDOException $Exception) {

                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {

            return null;
        }
    }

    /*=============================================
	Peticiones GET para selección de rangos
	=============================================*/

    static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {

        /*=============================================
		Validar existencia de la tabla y de las columnas
		=============================================*/

        $linkToArray = explode(",", $linkTo);

        if ($filterTo != null) {
            $filterToArray = explode(",", $filterTo);
        } else {
            $filterToArray = array();
        }

        $selectArray = explode(",", $select);

        foreach ($linkToArray  as $key => $value) {
            array_push($selectArray, $value);
        }

        foreach ($filterToArray  as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        if (empty(Connection::getColumnsData($table, $selectArray))) {

            return null;
        }

        $filter = "";

        if ($filterTo != null && $inTo != null) {

            $filter = 'AND ' . $filterTo . ' IN (' . $inTo . ')';
        }

        /*=============================================
		Sin ordenar y sin limitar datos
		=============================================*/

        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";

        /*=============================================
		Ordenar datos sin limites
		=============================================*/

        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
        }

        /*=============================================
		Ordenar y limitar datos
		=============================================*/

        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }
        /*=============================================
		Limitar datos sin ordenar
		=============================================*/

        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        try {

            $stmt->execute();
        } catch (PDOException $Exception) {

            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*=============================================
	Peticiones GET para selección de rangos con relaciones
	=============================================*/

    static public function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {
        // Dividimos los parámetros recibidos en arrays
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $linkToArray = explode(",", $linkTo);

        $innerJoinText = "";

        // Validar la existencia de las relaciones entre tablas
        foreach ($relArray as $key => $value) {
            if (empty(Connection::getColumnsData($value, ["*"]))) {
                return null;
            }
            if ($key > 0) {
                $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . " = " . $value . ".id_" . $typeArray[$key] . " ";
            }
        }

        // Validar la existencia de las columnas
        $selectArray = explode(",", $select);
        foreach ($linkToArray as $key => $value) {
            array_push($selectArray, $value);
        }
        if ($filterTo != null) {
            $filterToArray = explode(",", $filterTo);
            foreach ($filterToArray as $value) {
                array_push($selectArray, $value);
            }
        }
        $selectArray = array_unique($selectArray);
        if (empty(Connection::getColumnsData($relArray[0], $selectArray))) {
            return null;
        }

        // Construimos el filtro si es necesario
        $filter = "";
        if ($filterTo != null && $inTo != null) {
            $filter = " AND " . $filterTo . " IN (" . $inTo . ")";
        }

        // Consulta sin ordenar ni limitar
        $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";

        // Ordenar datos sin límite
        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {
            $sql .= " ORDER BY $orderBy $orderMode";
        }

        // Ordenar y limitar datos
        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {
            $sql .= " ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        // Limitar datos sin ordenar
        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {
            $sql .= " LIMIT $startAt, $endAt";
        }

        // Preparamos la consulta
        $stmt = Connection::connect()->prepare($sql);

        // Ejecutamos y retornamos los datos
        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
