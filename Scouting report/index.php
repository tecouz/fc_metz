<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Fonction pour déterminer la couleur de fond en fonction de l'évaluation
function getBackgroundColor($evaluation)
{
    switch ($evaluation) {
        case 'A':
            return 'green';
        case 'B':
            return '#FAA40F';
        case 'C':
            return '#FB6107';
        case 'D':
            return 'red';
        default:
            return 'transparent';
    }
}

// Récupérer l'ID du joueur à partir de l'URL ou du cookie
$player_id = isset($_GET['player_id']) ? $_GET['player_id'] : (isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null);

// Stocker l'ID du joueur dans un cookie
if ($player_id !== null && isset($_GET['player_id'])) {
    setcookie('player_id', $player_id, time() + (86400 * 30), "/"); // Expire dans 30 jours
}

// Stocker l'ID du joueur dans un cookie et rediriger vers la même page avec l'ID dans l'URL
if ($player_id !== null && !isset($_GET['player_id'])) {
    setcookie('player_id', $player_id, time() + (86400 * 30), "/"); // Expire dans 30 jours
    header("Location: " . $_SERVER['PHP_SELF'] . "?player_id=" . $player_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Scout</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table.css">
</head>

<body>
    <?php include "../Nav/nav.php"; ?>

    <div class="containerPage">
        <?php if ($player_id !== null): ?>
        <div class="add-report-button">
            <a href="form.php?player_id=<?php echo $player_id; ?>" class="btn btn-primary">Ajouter un rapport de
                scout</a>
        </div>
        <?php endif; ?>

        <?php
        if ($player_id !== null) {
            $stmt = $db->prepare("SELECT sr.scouting_report_date, sr.scouting_report_name, sr.scouting_report_firstname, sr.scouting_report_pdf, sr.scouting_report_player_evaluation, p.player_name
                                  FROM scouting_report sr
                                  INNER JOIN player p ON sr.player_id = p.player_id
                                  WHERE sr.player_id = ?");
            $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
            $stmt->execute();

            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($reports) > 0) {
                $player_name = $reports[0]['player_name'];
                echo "<h3>Rapport de Scout pour " . htmlspecialchars($player_name) . "</h3>";
                foreach ($reports as $report) {
                    echo "<div class='report-card'>";
                    echo "<p><strong>Date :</strong> " . htmlspecialchars($report['scouting_report_date']) . "</p>";
                    echo "<p><strong>Scout :</strong> " . htmlspecialchars($report['scouting_report_name']) . " " . htmlspecialchars($report['scouting_report_firstname']) . "</p>";
                    echo "<p><strong>Évaluation :</strong> <span style='background-color: " . getBackgroundColor($report['scouting_report_player_evaluation']) . ";'>" . htmlspecialchars($report['scouting_report_player_evaluation']) . "</span></p>";
                    echo "<p><strong>PDF :</strong> <a href='" . htmlspecialchars($report['scouting_report_pdf']) . "' target='_blank'>Voir le PDF</a></p>";
                    echo "</div>";
                }
            } else {
                echo "Aucun rapport de scout trouvé pour ce joueur.";
            }
        } else {
            echo "Aucun joueur sélectionné.";
        }

        // Afficher le barème des couleurs sous forme de tableau
        echo "<div class='color-legend'>
        <h4>Barème des couleurs :</h4>
        <table border='1'>
            <tr>
                <th>Évaluation</th>
                <th>Description</th>
            </tr>
            <tr style='background-color: green;'>
                <td>A</td>
                <td>Top mondial+<br>Top mondial<br>Niveau Ligue des Champions<br>Ligue 1 top joueurs<br>Ligue 1 titulaire</td>
            </tr>
            <tr style='background-color: #FAA40F;'>
                <td>B</td>
                <td>Ligue 1 rotation<br>Ligue 2 top joueurs<br>Ligue 2 titulaire</td>
            </tr>
            <tr style='background-color: #FB6107;'>
                <td>C</td>
                <td>Ligue 2 rotation<br>National 1 top joueurs</td>
            </tr>
            <tr style='background-color: red;'>
                <td>D</td>
                <td>National 1 titulaire<br>National 1 rotation<br>National 2 titulaire<br>National 2 rotation<br>National 3 titulaire<br>National 3 rotation</td>
            </tr>
        </table>
      </div>";
        ?>
    </div>
</body>

</html>