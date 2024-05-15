<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Récupérer les données de l'événement envoyées par la requête AJAX
$eventData = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données ont été reçues correctement
if (!empty($eventData)) {
    $title = $eventData['title'];
    $date = $eventData['date'];
    $startTime = $eventData['startTime'];
    $endTime = $eventData['endTime'];
    $location = $eventData['location'];
    $participants = $eventData['participants'];
    $description = $eventData['description'];

    // Préparer la requête SQL pour insérer un nouvel événement
    $stmt = $db->prepare('INSERT INTO evenement (evenement_title, evenement_date, evenement_start_time, evenement_end_time, evenement_location, evenement_participants, evenement_description) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bindParam(1, $title, PDO::PARAM_STR);
    $stmt->bindParam(2, $date, PDO::PARAM_STR);
    $stmt->bindParam(3, $startTime, PDO::PARAM_STR);
    $stmt->bindParam(4, $endTime, PDO::PARAM_STR);
    $stmt->bindParam(5, $location, PDO::PARAM_STR);
    $stmt->bindParam(6, $participants, PDO::PARAM_STR);
    $stmt->bindParam(7, $description, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // L'événement a été ajouté avec succès
        // Rediriger vers la page d'accueil
        header("Location: index.php?page=2");
        exit();
    } else {
        // Une erreur s'est produite lors de l'insertion de l'événement
        echo "Une erreur s'est produite lors de l'ajout de l'événement.";
    }
} else {
    // Aucune donnée n'a été reçue
    echo "Aucune donnée n'a été reçue.";
}