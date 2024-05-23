<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $report_id = $_GET['id'];

    // Vérifier si l'utilisateur connecté a le droit de supprimer ce rapport
    $sql = "SELECT users_id FROM scouting_report WHERE scouting_report_id = :report_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":report_id", $report_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['users_id'] == $_SESSION['user_id']) {
        // L'utilisateur connecté est autorisé à supprimer ce rapport
        $sql = "DELETE FROM scouting_report WHERE scouting_report_id = :report_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":report_id", $report_id);
        $stmt->execute();
    } else {
        echo "Vous n'êtes pas autorisé à supprimer ce rapport de scout.";
    }
}

// Récupérer l'ID du joueur à partir du rapport supprimé
$sql = "SELECT player_id FROM scouting_report WHERE scouting_report_id = :report_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(":report_id", $report_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $player_id = $result['player_id'];
    header("Location: index.php?player_id=" . $player_id);
} else {
    header("Location: index.php");
}
exit;