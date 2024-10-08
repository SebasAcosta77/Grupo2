<?php
class PutModel{
    static public function putModel($tabla, $data, $id, $nameId){
        $response=GetModel::getDataFilter($tabla, $nameId, $nameId, $id, null, null, null, null);
        if (empty($response)) {
            return null;
        }
        //actualizar registros
        $set ="";
        foreach ($data as $key => $value) {
            $set.=$key." = '".$key."', ";
        }

        $set = substr($set,0, -1);
        $sql = "UPDATE $tabla SET $set WHERE $nameId =: $nameId";

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