<?php
// Connexion à la base de données
$servername = "localhost"; // ou votre adresse IP
$username = "root";
$password = "";
$database = "fc_metz";

$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
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
    <div class="containerPage">
        <div>
            <h1>Statistique Spécifiques Saison 2023/2024 du joueur</h1>
            <p>Statistique mises à jour après chaque match</p>
        </div>

        <div class="container">
            <?php
            // ID du joueur dont vous voulez afficher les statistiques
            $player_id = 1; // Remplacez 1 par l'ID du joueur souhaité
            
            // Requête SQL pour récupérer les statistiques du joueur spécifique
            $sql = "SELECT * FROM statistic WHERE player_id = $player_id";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Afficher les statistiques dans les cartes
                while ($row = $result->fetch_assoc()) {
                    // Afficher une carte pour chaque statistique disponible
                    foreach ($row as $key => $value) {
                        if ($key !== 'player_id' && $key !== 'statistic_id' && $value !== null) {
                            echo "<div class='carte'>";
                            echo "<h3>" . ucwords(str_replace('_', ' ', $key)) . "</h3>";
                            echo "<p>" . $value . "</p>";
                            echo "</div>";
                        }
                    }
                }
            } else {
                echo "Aucune statistique trouvée pour ce joueur";
            }
            ?>
        </div>
    </div>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>