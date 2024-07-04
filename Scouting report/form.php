<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

// Récupérer l'ID du joueur à partir du cookie
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;

$report_id = 0;
$report_date = "";
$report_name = "";
$report_firstname = "";
$report_pdf = "";
$scouting_report_player_evaluation = "";
// Initialiser les variables pour stocker les informations du rapport de scout

// Récupérer les informations du rapport existant si un ID est fourni
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $sql = "SELECT * FROM scouting_report WHERE scouting_report_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([":id" => $_GET['id']]);
    if ($row = $stmt->fetch()) {
        $report_id = $_GET['id'];
        $report_date = $row["scouting_report_date"];
        $report_name = $row["scouting_report_name"];
        $report_firstname = $row["scouting_report_firstname"];
        $report_pdf = $row["scouting_report_pdf"];
        $scouting_report_player_evaluation = $row["scouting_report_player_evaluation"];
    }
}
// Si un ID de rapport est fourni dans l'URL, récupérer les informations de ce rapport depuis la base de données
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Rapport de Scout</title>
</head>

<body>
    <a href="index.php?player_id=<?php echo $player_id; ?>" title="Retour">Retour</a>
    <!-- Lien de retour vers la page du joueur avec l'ID du joueur dans l'URL -->

    <form action="process.php" method="post" enctype="multipart/form-data">
        <label for="report_date">Date :</label>
        <input type="date" name="report_date" id="report_date" value="<?php echo $report_date; ?>">
        <!-- Champ de saisie de date pour le rapport, pré-rempli avec la date existante -->

        <label for="report_name">Nom du scout :</label>
        <input type="text" name="report_name" id="report_name" value="<?php echo $report_name; ?>">
        <!-- Champ de saisie pour le nom du scout, pré-rempli avec le nom existant -->

        <label for="report_firstname">Prénom du scout :</label>
        <input type="text" name="report_firstname" id="report_firstname" value="<?php echo $report_firstname; ?>">
        <!-- Champ de saisie pour le prénom du scout, pré-rempli avec le prénom existant -->

        <label for="report_pdf">Rapport PDF :</label>
        <input type="file" name="report_pdf" id="report_pdf">
        <?php if (!empty($report_pdf)): ?>
            <p>Rapport PDF actuel : <a href="<?php echo $report_pdf; ?>" target="_blank">Voir le PDF</a></p>
        <?php endif; ?>
        <!-- Champ de saisie pour le fichier PDF du rapport, avec un lien pour voir le PDF existant -->

        <label for="scouting_report_player_evaluation">Évaluation du joueur :</label>
        <select name="scouting_report_player_evaluation" id="scouting_report_player_evaluation">
            <option value="A" <?php echo ($scouting_report_player_evaluation == 'A') ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo ($scouting_report_player_evaluation == 'B') ? 'selected' : ''; ?>>B</option>
            <option value="C" <?php echo ($scouting_report_player_evaluation == 'C') ? 'selected' : ''; ?>>C</option>
            <option value="D" <?php echo ($scouting_report_player_evaluation == 'D') ? 'selected' : ''; ?>>D</option>
        </select>
        <!-- Liste déroulante pour sélectionner l'évaluation du joueur, avec l'option existante sélectionnée -->

        <button type="submit">Valider</button>
        <input type="hidden" name="report_id" value="<?php echo $report_id; ?>">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
        <!-- Bouton de soumission du formulaire et champs cachés pour transmettre l'ID du rapport et l'ID du joueur -->
    </form>
</body>

</html>