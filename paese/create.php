<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/paesi.php';

//crea un Paese sul database

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nome)) {
    $paese->nome = $data->nome;

    if ($paese->create()) {
        echo json_encode(["message" => "Paese creato con successo."]);
    } else {
        echo json_encode(["message" => "Impossibile creare il paese."]);
    }
} else {
    echo json_encode(["message" => "Dati incompleti."]);
}
?>

