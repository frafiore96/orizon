<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/viaggi.php';

//crea un Viaggio sul database

$database = new Database();
$db = $database->getConnection();

$viaggio = new Viaggio($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->paese_partenza) && !empty($data->paese_arrivo) && !empty($data->posti_disponibili)) {
    $viaggio->paese_partenza = $data->paese_partenza;
    $viaggio->paese_arrivo = $data->paese_arrivo;
    $viaggio->posti_disponibili = $data->posti_disponibili;

    if ($viaggio->create()) {
        echo json_encode(["message" => "Viaggio creato con successo."]);
    } else {
        echo json_encode(["message" => "Impossibile creare il viaggio. Assicurati che i paesi esistano."]);
    }
} else {
    echo json_encode(["message" => "Dati incompleti."]);
}
?>
