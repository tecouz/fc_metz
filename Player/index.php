<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
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
        <div>
            <h1>Informations du joueur</h1>
        </div>

        <div class="container">
            <?php
            $stmt = $db->prepare("SELECT * FROM player WHERE player_id = ?");
            $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
            $player_id = 1;
            $stmt->execute();

            // Récupération des résultats
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Affichage des informations du joueur
            if (count($result) > 0) {
                $row = $result[0];
                foreach ($row as $key => $value) {
                    // Exclure player_id et player_image
                    if ($key !== 'player_id' && $key !== 'player_image') {
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
            ?>
        </div>
    </div>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$db = null;
?>