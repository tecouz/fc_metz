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

// Récupérer les informations du joueur à partir de l'API WyScout
$competitionId = 123; // Remplacez par l'ID de la compétition appropriée
$playerId = $player_id; // Utilisez l'ID du joueur récupéré précédemment

$apiUrl = "https://apirest.wyscout.com/v3/competitions/$competitionId/players/$playerId";
$headers = array(
    "Authorization: Bearer <votre_jeton_d_accès>",
    "Content-Type: application/json"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$playerData = json_decode($response, true);
$playerName = '';

// Vérifier si les données du joueur sont disponibles
if (!is_null($playerData) && isset($playerData['firstName']) && isset($playerData['lastName'])) {
    $playerName = $playerData['firstName'] . ' ' . $playerData['lastName'];
} else {
    // Gérer le cas où les données du joueur ne sont pas disponibles
    $playerName = 'Nom du joueur indisponible';
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
    <?php include "../Nav/nav.php"; ?>

    <div class="containerPage">
        <h1>Notes d'informations</h1>

        <?php
        // Récupérer l'historique des transferts depuis l'API WyScout
        $transfers_url = 'https://apirest.wyscout.com/v3/players/' . $player_id . '/transfers';
        $transfers_response = @file_get_contents($transfers_url, false, stream_context_create(
            array(
                'http' => array(
                    'header' => "Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg==\r\n"
                )
            )
        )
        );

        if ($transfers_response !== false) {
            $transfers_data = json_decode($transfers_response, true);

            if (!empty($transfers_data['transfer'])) {
                echo "<h2>Historique des transferts</h2>";
                echo "<table>";
                echo "<tr><th>Date de début</th><th>Date de fin</th><th>Équipe de départ</th><th>Équipe d'arrivée</th><th>Type</th><th>Valeur</th></tr>";

                foreach ($transfers_data['transfer'] as $transfer) {
                    $start_date = isset($transfer['startDate']) ? $transfer['startDate'] : '';
                    $end_date = isset($transfer['endDate']) ? $transfer['endDate'] : '';
                    $team_from_name = isset($transfer['fromTeamName']) ? htmlspecialchars($transfer['fromTeamName']) : '';
                    $team_to_name = isset($transfer['toTeamName']) ? htmlspecialchars($transfer['toTeamName']) : '';
                    $transfer_type = isset($transfer['type']) ? $transfer['type'] : '';
                    $transfer_value = isset($transfer['value']) ? $transfer['value'] : '0';
                    $transfer_currency = isset($transfer['currency']) ? $transfer['currency'] : '';

                    echo "<tr>";
                    echo "<td>$start_date</td>";
                    echo "<td>$end_date</td>";
                    echo "<td>$team_from_name</td>";
                    echo "<td>$team_to_name</td>";
                    echo "<td>$transfer_type</td>";
                    echo "<td>$transfer_value $transfer_currency</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Aucun historique de transfert trouvé pour ce joueur.";
            }
        } else {
            echo "Erreur lors de la récupération de l'historique des transferts.";
        }
        ?>

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
        $sql = "SELECT n.note_content, n.note_date, u.users_name
                FROM note n
                JOIN users u ON n.users_id = u.users_id
                WHERE n.player_id = :player_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":player_id", $player_id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result && $stmt->rowCount() > 0) {
            // Afficher les notes dans un tableau HTML
            echo "<h3>Notes pour " . htmlspecialchars($playerName) . "</h3>";
            echo "<table>";
            echo "<tr><th>Note Content</th><th>Note Date</th><th>User Name</th></tr>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['note_content'] . "</td>";
                echo "<td>" . $row['note_date'] . "</td>";
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