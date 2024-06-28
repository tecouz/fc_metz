<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Récupérer l'ID du joueur à partir du cookie
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;

$player_contact_agence = '';
$player_agence = '';
$player_contrat = '';

// Récupérer les informations du joueur depuis la base de données
if ($player_id !== null) {
    $sql = "SELECT player_contact_agence, player_agence, player_contrat FROM player WHERE player_wyId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $player_contact_agence = $result['player_contact_agence'];
        $player_agence = $result['player_agence'];
        $player_contrat = $result['player_contrat'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier les informations du joueur</title>
</head>

<body>
    <h1>Modifier les informations du joueur</h1>

    <form method="post" action="process.php">
        <label for="player_contact_agence">Contact de l'agence :</label>
        <input type="text" id="player_contact_agence" name="player_contact_agence"
            value="<?php echo $player_contact_agence; ?>"><br>

        <label for="player_agence">Agence :</label>
        <input type="text" id="player_agence" name="player_agence" value="<?php echo $player_agence; ?>"><br>

        <label for="player_contrat">Date de contrat :</label>
        <input type="date" id="player_contrat" name="player_contrat" value="<?php echo $player_contrat; ?>"><br>

        <input type="submit" value="Enregistrer">
    </form>
</body>

</html>