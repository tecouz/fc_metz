<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Récupérer le mois et l'année depuis les paramètres de l'URL
$month = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;
function getEventsForMonth($month, $year)
{
    global $db;

    // Préparer la requête SQL pour récupérer les événements du mois spécifié
    $stmt = $db->prepare("SELECT evenement_title AS title,
                             DATE_FORMAT(evenement_date, '%Y-%m-%d') AS event_date,
                             evenement_start_time AS start_time,
                             evenement_end_time AS end_time,
                             evenement_participants AS participants
                      FROM evenement
                      WHERE MONTH(evenement_date) = ? AND YEAR(evenement_date) = ?");

    $stmt->bindParam(1, $month, PDO::PARAM_INT);
    $stmt->bindParam(2, $year, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $events;
}



// Récupérer les événements pour le mois et l'année spécifiés
if ($month !== null && $year !== null) {
    $events = getEventsForMonth($month, $year);
    echo json_encode($events);
} else {
    echo json_encode([]);
}