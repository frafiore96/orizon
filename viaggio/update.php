<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/viaggi.php';

//aggionra un Viaggio sul database

$database = new Database();
$db = $database->getConnection();

$viaggio = new Viaggio($db);

$data = json_decode(file_get_contents("php://input"));

// Controlla se i dati necessari sono forniti
if (!empty($data->paese_partenza) && !empty($data->paese_arrivo) && isset($data->posti_disponibili)) {
    // Imposta i dati del viaggio
    $viaggio->paese_partenza = $data->paese_partenza;
    $viaggio->paese_arrivo = $data->paese_arrivo;
    $viaggio->posti_disponibili = $data->posti_disponibili;

    // Verifica se il viaggio esiste
    $stmt = $viaggio->read($viaggio->paese_partenza, $viaggio->paese_arrivo);
    $existingJourney = $stmt->rowCount() > 0; // Verifica se ci sono risultati

    if ($existingJourney) {
        // Modifica il viaggio con i nuovi dati
        if ($viaggio->update()) {
            echo json_encode(["message" => "Viaggio aggiornato con successo."]);
        } else {
            echo json_encode(["message" => "Impossibile aggiornare il viaggio."]);
        }
    } else {
        echo json_encode(["message" => "Il viaggio specificato non esiste."]);
    }
} else {
    echo json_encode(["message" => "Dati incompleti."]);
}
?>

