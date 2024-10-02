<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/paesi.php';

//aggiorna un Paese sul database

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->old_nome) && !empty($data->nome)) {
    $paese->old_nome = $data->old_nome;
    $paese->nome = $data->nome;

    if ($paese->update()) {
        echo json_encode(["message" => "Paese aggiornato con successo."]);
    } else {
        echo json_encode(["message" => "Impossibile aggiornare il paese."]);
    }
} else {
    echo json_encode(["message" => "Dati incompleti."]);
}
?>
