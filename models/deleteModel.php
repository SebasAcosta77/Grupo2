<?php

class DeleteModel{
    static public function deleteData($tabla, $id, $nameId){
        $respnse = GetModel::getDataFilter($tabla, $nameId, $nameId, $id, null, null, null, null);
        if (empty($respnse)){
            return null;
        }

        $sql ="DELETE FROM $tabla WHERE $nameId=:$nameId";

        $link = Connection::connect();
        $stmp = $link->prepare($sql);
        $stmp->bindParam(':'.$nameId, $id, PDO::PARAM_STR);
        if ($stmp->execute()) {
            $response= array(
                "lastid" => $link -> lastInsertId(),
                "coment" => "Proceso exitoso"
            );
            return $response;
        }else{
            return $link -> errorInfo();
        }
    }
}
?>