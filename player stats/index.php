<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Récupérer l'ID du joueur à partir de l'URL ou du cookie
$player_id = isset($_GET['player_id']) ? $_GET['player_id'] : (isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null);

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
    <title>Statistique</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Mitr:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/card.css">
</head>

<body>
    <?php include "../Nav/nav.php"; ?>
    <div class="containerPage">
        <div>
            <h1>Statistique Spécifiques Saison 2023/2024 du joueur</h1>
            <p>Statistique mises à jour après chaque match</p>
        </div>

        <div class="container">
            <?php
            // Vérifier si l'ID du joueur est présent
            if ($player_id !== null) {
                $stmt = $db->prepare("SELECT s.*, p.player_name 
                                      FROM statistic s
                                      JOIN player p ON s.player_id = p.player_id
                                      WHERE s.player_id = ?");
                $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
                $stmt->execute();

                // Récupération des résultats
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Boucle d'affichage des résultats
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        foreach ($row as $key => $value) {
                            if ($key !== 'player_id' && $key !== 'statistic_id' && $key !== 'player_name' && $value !== null) {
                                // Échappement HTML
                                $key = htmlspecialchars($key);
                                $value = htmlspecialchars($value);

                                echo "<div class='carte'>";
                                echo "<h3>" . ucwords(str_replace('_', ' ', $key)) . "</h3>";
                                echo "<p>$value</p>";
                                echo "</div>";
                            }
                        }
                    }
                } else {
                    echo "Aucune statistique trouvée";
                }
            } else {
                echo "Aucun joueur sélectionné";
            }
            ?>
        </div>
    </div>
</body>

</html>