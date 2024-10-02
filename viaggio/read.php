<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/viaggi.php';

//legge e filtra un Viaggio sul database

$database = new Database();
$db = $database->getConnection();

$viaggio = new Viaggio($db);

$paese_partenza = isset($_GET['paese_partenza']) ? htmlspecialchars(strip_tags($_GET['paese_partenza'])) : null;
$paese_arrivo = isset($_GET['paese_arrivo']) ? htmlspecialchars(strip_tags($_GET['paese_arrivo'])) : null;
$posti_disponibili = isset($_GET['posti_disponibili']) ? htmlspecialchars(strip_tags($_GET['posti_disponibili'])) : null;

$stmt = $viaggio->read($paese_partenza, $paese_arrivo, $posti_disponibili);
$num = $stmt->rowCount();

if ($num > 0) {
    $viaggi_arr = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $viaggio_item = [
            "paese_partenza" => $paese_partenza,
            "paese_arrivo" => $paese_arrivo,
            "posti_disponibili" => $posti_disponibili
        ];

        array_push($viaggi_arr, $viaggio_item);
    }

    echo json_encode($viaggi_arr);
} else {
    echo json_encode(["message" => "Nessun viaggio trovato."]);
}
?>





