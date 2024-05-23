<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

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
    <title>Statistique</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Mitr:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/card.css">
</head>

<body>
    <?php
    include "../Nav/nav.php";
    ?>
    <div class="containerPage">
        <div class="player-header">
            <h1>Informations du joueur</h1>
            <?php if ($player_id !== null): ?>
                <a href="../CRUD/process.php?player_id=<?php echo $player_id; ?>" class="edit-button">Modifier</a>

            <?php endif; ?>
        </div>

        <div class="container">
            <?php
            // Vérifier si l'ID du joueur est présent
            if ($player_id !== null) {
                $stmt = $db->prepare("SELECT * FROM player WHERE player_id = ?");
                $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
                $stmt->execute();

                // Récupération des résultats
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Affichage des informations du joueur
                if (count($result) > 0) {
                    $row = $result[0];
                    foreach ($row as $key => $value) {
                        // Exclure player_id, player_image et player_evaluation
                        if ($key !== 'player_id' && $key !== 'player_image' && $key !== 'player_evaluation') {
                            // Échappement HTML
                            $key = htmlspecialchars($key);
                            $value = htmlspecialchars($value);
                            echo "<div class='carte'>";
                            echo "<h3>" . ucwords(str_replace('_', ' ', $key)) . "</h3>";
                            echo "<p>$value</p>";
                            echo "</div>";
                        }
                    }
                } else {
                    echo "Aucune information trouvée pour ce joueur";
                }
            } else {
                echo "Aucun joueur sélectionné";
            }
            ?>
        </div>
    </div>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$db = null;
?>