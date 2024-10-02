<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/paesi.php';

//legge un Paese sul database

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

$stmt = $paese->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $paesi_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $paesi_arr[] = $nome;
    }
    echo json_encode($paesi_arr);
} else {
    echo json_encode(["message" => "Nessun paese trovato."]);
}
?>

