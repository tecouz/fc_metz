<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Récupérer l'ID du joueur à partir du cookie
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;

$note_id = 0;
$note_content = "";
$note_date = "";

// Récupérer les informations de la note existante si un ID est fourni
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $sql = "SELECT * FROM note WHERE note_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([":id" => $_GET['id']]);
    if ($row = $stmt->fetch()) {
        $note_id = $_GET['id'];
        $note_content = $row["note_content"];
        $note_date = $row["note_date"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Note</title>
</head>

<body>
    <a href="../Player/index.php?player_id=<?php echo $player_id; ?>" title="Retour">Retour</a>
    <form action="process.php" method="post">
        <label for="note_content">Contenu de la note :</label>
        <textarea name="note_content" id="note_content"
            placeholder="Entrez le contenu de la note"><?php echo $note_content; ?></textarea>

        <label for="note_date">Date :</label>
        <input type="date" name="note_date" id="note_date" value="<?php echo $note_date; ?>">

        <button type="submit">Valider</button>
        <input type="hidden" name="note_id" value="<?php echo $note_id; ?>">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
    </form>
</body>

</html>