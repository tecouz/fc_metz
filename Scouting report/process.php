<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $report_id = isset($_POST["report_id"]) ? $_POST["report_id"] : 0;
    $report_date = $_POST["report_date"];
    $report_name = $_POST["report_name"];
    $report_firstname = $_POST["report_firstname"];
    $player_id = $_POST["player_id"];
    $scouting_report_player_evaluation = $_POST["scouting_report_player_evaluation"];

    // Gérer le téléchargement du fichier PDF
    $report_pdf = "";
    if (isset($_FILES["report_pdf"]) && $_FILES["report_pdf"]["error"] == 0) {
        $upload_dir = "../upload/"; // Dossier de destination pour les fichiers téléchargés
        $file_name = basename($_FILES["report_pdf"]["name"]);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["report_pdf"]["tmp_name"], $upload_file)) {
            $report_pdf = $upload_file;
        } else {
            echo "Une erreur s'est produite lors du téléchargement du fichier PDF.";
        }
    }

    // Préparer la requête SQL en fonction de l'action (création ou mise à jour)
    if ($report_id > 0) {
        // Mise à jour d'un rapport existant
        $sql = "UPDATE scouting_report SET
                scouting_report_date = :report_date,
                scouting_report_name = :report_name,
                scouting_report_firstname = :report_firstname,
                scouting_report_pdf = :report_pdf,
                player_id = :player_id,
                scouting_report_player_evaluation = :scouting_report_player_evaluation
                WHERE scouting_report_id = :report_id";
    } else {
        // Création d'un nouveau rapport
        $sql = "INSERT INTO scouting_report (
                scouting_report_date,
                scouting_report_name,
                scouting_report_firstname,
                scouting_report_pdf,
                player_id,
                scouting_report_player_evaluation
                ) VALUES (
                :report_date,
                :report_name,
                :report_firstname,
                :report_pdf,
                :player_id,
                :scouting_report_player_evaluation
                )";
    }

    // Préparer et exécuter la requête SQL pour le rapport de scout
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":report_date", $report_date);
    $stmt->bindParam(":report_name", $report_name);
    $stmt->bindParam(":report_firstname", $report_firstname);
    $stmt->bindParam(":report_pdf", $report_pdf);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->bindParam(":scouting_report_player_evaluation", $scouting_report_player_evaluation);

    if ($report_id > 0) {
        $stmt->bindParam(":report_id", $report_id);
    }

    if ($stmt->execute()) {
        // Mettre à jour l'évaluation du joueur dans la table player
        $sql_update_player = "UPDATE player SET player_evaluation = :player_evaluation WHERE player_id = :player_id";
        $stmt_update_player = $db->prepare($sql_update_player);
        $stmt_update_player->bindParam(":player_evaluation", $scouting_report_player_evaluation);
        $stmt_update_player->bindParam(":player_id", $player_id);
        $stmt_update_player->execute();

        // Rediriger vers la page d'index des rapports de scout
        header("Location: index.php?player_id=" . $player_id);
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'enregistrement du rapport de scout.";
    }
}
?>