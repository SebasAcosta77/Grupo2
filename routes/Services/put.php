<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

if (isset($_GET["id"]) && isset ($_GET["nameId"])) {
    $data=array();
parse_str(file_get_contents('php://input'),$data);
}

$columns=array();
foreach (array_keys($data) as $key => $values) {
    array_push($columns,$values);
}

array_push($columns, $_GET["nameId"]);
$columns=array_unique($columns);

if (empty(Connection::getColumnsData($table, $columns))) {
    $json=array(
        "status"=> 400,
        'result'=>"Los datos no coinciden"
    );
    echo json_encode($json, http_response_code($json["status"]));
}

?>
