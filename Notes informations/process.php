<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

var_dump($_SESSION['user_id']);

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $note_id = isset($_POST["note_id"]) ? $_POST["note_id"] : 0;
    $note_content = $_POST["note_content"];
    $note_date = $_POST["note_date"];
    $player_id = $_POST["player_id"];
    $users_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

    // Préparer la requête SQL en fonction de l'action (création ou mise à jour)
    if ($note_id > 0) {
        // Mise à jour d'une note existante
        $sql = "UPDATE note SET
                note_content = :note_content,
                note_date = :note_date,
                player_id = :player_id,
                users_id = :users_id
                WHERE note_id = :note_id";
    } else {
        // Création d'une nouvelle note
        $sql = "INSERT INTO note (
                note_content,
                note_date,
                player_id,
                users_id
                ) VALUES (
                :note_content,
                :note_date,
                :player_id,
                :users_id
                )";
    }

    // Préparer et exécuter la requête SQL
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":note_content", $note_content);
    $stmt->bindParam(":note_date", $note_date);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->bindParam(":users_id", $users_id);

    if ($note_id > 0) {
        $stmt->bindParam(":note_id", $note_id);
    }

    if ($stmt->execute()) {
        // Rediriger vers la page d'index des notes
        header("Location: ../Notes informations/index.php");
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'enregistrement de la note.";
    }
}
?>