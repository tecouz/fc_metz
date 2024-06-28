<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Récupérer l'ID du joueur à partir du cookie
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;

// Récupérer les données du formulaire
$player_contact_agence = $_POST['player_contact_agence'];
$player_agence = $_POST['player_agence'];
$player_contrat = $_POST['player_contrat'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si le player_id existe déjà dans la base de données
    $sql = "SELECT player_id FROM player WHERE player_wyId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Le player_id existe déjà, mettre à jour les informations
        $existing_player_id = $result['player_id'];

        $sql = "UPDATE player SET player_contact_agence = ?, player_agence = ?, player_contrat = ? WHERE player_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $player_contact_agence, PDO::PARAM_STR);
        $stmt->bindParam(2, $player_agence, PDO::PARAM_STR);
        $stmt->bindParam(3, $player_contrat, PDO::PARAM_STR);
        $stmt->bindParam(4, $existing_player_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("location: index.php?player_id=" . $player_id);
            exit;
        } else {
            echo "Erreur : " . $stmt->errorInfo()[2];
        }
    } else {
        // Le player_id n'existe pas, ajouter un nouvel enregistrement
        if ($player_id !== null) {
            $sql = "INSERT INTO player (player_wyId, player_contact_agence, player_agence, player_contrat) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $player_contact_agence, PDO::PARAM_STR);
            $stmt->bindParam(3, $player_agence, PDO::PARAM_STR);
            $stmt->bindParam(4, $player_contrat, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $new_player_id = $db->lastInsertId();
                header("location: index.php?player_id=" . $player_id);
                exit;
            } else {
                echo "Erreur : " . $stmt->errorInfo()[2];
            }
        } else {
            echo "Erreur : Le cookie 'player_id' n'est pas défini.";
        }
    }
}
?>