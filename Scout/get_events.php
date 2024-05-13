<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Récupérer la date à partir de la requête GET
$eventDate = isset($_GET['date']) ? $_GET['date'] : null;

if ($eventDate !== null) {
    // Préparer et exécuter la requête SQL pour récupérer les événements pour la date donnée
    $stmt = $db->prepare('SELECT evenement_title AS title, evenement_start_time AS start_time, evenement_end_time AS end_time FROM evenement WHERE evenement_date = ?');
    $stmt->bindParam(1, $eventDate, PDO::PARAM_STR);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les événements au format JSON
    header('Content-Type: application/json');
    echo json_encode($events);
} else {
    echo "Date invalide.";
}