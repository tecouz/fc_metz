<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

// Récupérer l'ID du joueur à partir du cookie
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;
// Si le cookie 'player_id' existe, récupérer sa valeur, sinon définir $player_id à null

$note_id = 0;
$note_content = "";
$note_date = "";
// Initialiser les variables pour stocker les informations de la note

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
// Si un ID de note est fourni dans l'URL, récupérer les informations de cette note depuis la base de données
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
    <!-- Lien de retour vers la page du joueur avec l'ID du joueur dans l'URL -->

    <form action="process.php" method="post">
        <label for="note_content">Contenu de la note :</label>
        <textarea name="note_content" id="note_content"
            placeholder="Entrez le contenu de la note"><?php echo $note_content; ?></textarea>
        <!-- Champ textarea pour saisir le contenu de la note, pré-rempli avec le contenu existant -->

        <label for="note_date">Date :</label>
        <input type="date" name="note_date" id="note_date" value="<?php echo $note_date; ?>">
        <!-- Champ de saisie de date pour la note, pré-rempli avec la date existante -->

        <button type="submit">Valider</button>
        <input type="hidden" name="note_id" value="<?php echo $note_id; ?>">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
        <!-- Bouton de soumission du formulaire et champs cachés pour transmettre l'ID de la note et l'ID du joueur -->
    </form>
</body>

</html>