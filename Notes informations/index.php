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
    <title>Note</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/copy.css">
    <link rel="stylesheet" href="../css/table.css">
</head>

<body>
    <?php
    include "../Nav/nav.php";
    ?>

    <div class="containerPage">

        <h1>Notes d'informations</h1>

        <p>historique des transfert (pas faisable sans l'api)</p>

        <div>
            <p>historique des blessures</p>
            <a href="https://noisefeed.com" target="_blank">Noisefeed</a>
            <div class="card">
                <div class="card-body">
                    <div class="login-info">
                        <p><strong>Identifiant :</strong> IdentifiantFcM</p>
                        <p><strong>Mot de passe :</strong> <input type="password" id="password-input"
                                value="motdepasse123" readonly></p>
                    </div>
                    <div class="copy-buttons">
                        <button class="copy-btn" id="copy-username">
                            <i class="fa-solid fa-copy"></i> Copier l'identifiant
                        </button>
                        <button class="copy-btn" id="copy-password">
                            <i class="fa-solid fa-copy"></i> Copier le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Requête SQL pour récupérer les notes du joueur sélectionné
        $sql = "SELECT n.note_content, n.note_date, CONCAT(p.player_name, ' ', p.player_firstname) AS player_fullname, u.users_name
                FROM note n
                JOIN player p ON n.player_id = p.player_id
                JOIN users u ON n.users_id = u.users_id
                WHERE n.player_id = :player_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":player_id", $player_id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result && $stmt->rowCount() > 0) {
            // Afficher les notes dans un tableau HTML
            echo "<table>";
            echo "<tr><th>Note Content</th><th>Note Date</th><th>Player Name</th><th>User Name</th></tr>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['note_content'] . "</td>";
                echo "<td>" . $row['note_date'] . "</td>";
                echo "<td>" . $row['player_fullname'] . "</td>"; // Afficher le nom complet du joueur
                echo "<td>" . $row['users_name'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "Aucune note trouvée pour ce joueur.";
        }
        ?>

        <?php if ($player_id !== null): ?>
            <a href="form.php?player_id=<?php echo $player_id; ?>">Ajouter une note</a>
        <?php endif; ?>
    </div>
    <script src="../js/copy.js"></script>
</body>

</html>

<?php
// Fermer la connexion à la base de données
$db = null;
?>