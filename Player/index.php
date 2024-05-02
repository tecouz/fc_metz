<?php
// Connexion à la base de données
$servername = "localhost"; // ou votre adresse IP
$username = "root"; //nom d'utilisateur
$password = ""; // MDP
$database = "fc_metz"; // nom de la base de donnée

$conn = new mysqli($servername, $username, $password, $database); // requête SQL de connexion a la base de donnée

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
    <?php
    include "../Nav/nav.php";
    ?>
    <div class="containerPage">
        <div>
            <h1>Informations du joueur</h1>
        </div>

        <div class="container">
            <?php
            $stmt = $conn->prepare("SELECT * FROM player WHERE player_id = ?");
            $stmt->bind_param("i", $player_id);
            $player_id = 1;
            // Exécution
            $stmt->execute();

            // Récupération des résultats
            $result = $stmt->get_result();

            // Affichage des informations du joueur
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
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
$conn->close();
?>