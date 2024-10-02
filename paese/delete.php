<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/paesi.php';

//elimina un Paese sul database

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nome)) {
    $paese->nome = $data->nome;

    if ($paese->delete()) {
        echo json_encode(["message" => "Paese eliminato con successo."]);
    } else {
        echo json_encode(["message" => "Impossibile eliminare il paese."]);
    }
} else {
    echo json_encode(["message" => "Dati incompleti."]);
}
?>

