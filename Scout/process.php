<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $event_id = isset($_POST["event_id"]) ? $_POST["event_id"] : 0;
    $event_title = $_POST["event_title"];
    $event_description = $_POST["event_description"];
    $event_date = $_POST["event_date"];
    $event_start_time = $_POST["event_start_time"];
    $event_end_time = $_POST["event_end_time"];
    $event_location = $_POST["event_location"];
    $event_participants = isset($_POST['event_participants']) ? $_POST['event_participants'] : array();

    // Vérifier si $event_participants est un tableau
    if (is_array($event_participants)) {
        // Convertir le tableau en une chaîne de caractères séparée par des virgules
        $participants_string = implode(',', $event_participants);
    } else {
        // Sinon, utiliser la valeur telle quelle
        $participants_string = $event_participants;
    }

    // Préparer la requête SQL en fonction de l'action (création ou mise à jour)
    if ($event_id > 0) {
        // Mise à jour d'un événement existant
        $sql = "UPDATE evenement SET
                evenement_title = :event_title,
                evenement_description = :event_description,
                evenement_date = :event_date,
                evenement_start_time = :event_start_time,
                evenement_end_time = :event_end_time,
                evenement_location = :event_location,
                evenement_participants = :event_participants
                WHERE evenement_id = :event_id";
    } else {
        // Création d'un nouvel événement
        $sql = "INSERT INTO evenement (
                evenement_title,
                evenement_description,
                evenement_date,
                evenement_start_time,
                evenement_end_time,
                evenement_location,
                evenement_participants
                ) VALUES (
                :event_title,
                :event_description,
                :event_date,
                :event_start_time,
                :event_end_time,
                :event_location,
                :event_participants
                )";
    }

    // Préparer et exécuter la requête SQL
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":event_title", $event_title);
    $stmt->bindParam(":event_description", $event_description);
    $stmt->bindParam(":event_date", $event_date);
    $stmt->bindParam(":event_start_time", $event_start_time);
    $stmt->bindParam(":event_end_time", $event_end_time);
    $stmt->bindParam(":event_location", $event_location);
    $stmt->bindParam(":event_participants", $participants_string);

    if ($event_id > 0) {
        $stmt->bindParam(":event_id", $event_id);
    }

    if ($stmt->execute()) {
        // Rediriger vers la page d'index avec la pagination du calendrier
        header("Location: index.php?page=2");
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'enregistrement de l'événement.";
    }
}
?>