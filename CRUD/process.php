<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

$player_id = isset($_GET['player_id']) ? $_GET['player_id'] : null;

// Initialiser la variable $player_evaluation avec une valeur par défaut
$player_evaluation = 'A';

// Initialiser les variables pour stocker les valeurs des champs du formulaire
$player_name = $player_firstname = $player_birthday = $player_club = $player_height = $player_weight = $player_origin = $player_favorite_foot = $player_position = $player_favorite_position = $player_percentage_position = $player_date_expiration_contract = $player_agence = $player_contact_agence = $player_national_team = $player_market_value = $player_image = '';

// Si un ID de joueur est fourni, récupérer les données du joueur depuis la base de données
if ($player_id !== null) {
    $stmt = $db->prepare("SELECT * FROM player WHERE player_id = ?");
    $stmt->bindParam(1, $player_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $player_name = $result['player_name'];
        $player_firstname = $result['player_firstname'];
        $player_birthday = date('Y-m-d', strtotime($result['player_birthday']));
        $player_club = $result['player_club'];
        $player_height = $result['player_height'];
        $player_weight = $result['player_weight'];
        $player_origin = $result['player_origin'];
        $player_favorite_foot = $result['player_favorite_foot'];
        $player_position = $result['player_position'];
        $player_favorite_position = $result['player_favorite_position'];
        $player_percentage_position = $result['player_percentage_position'];
        $player_date_expiration_contract = date('Y-m-d', strtotime($result['player_date_expiration_contract']));
        $player_agence = $result['player_agence'];
        $player_contact_agence = $result['player_contact_agence'];
        $player_national_team = $result['player_national_team'];
        $player_market_value = $result['player_market_value'];
        $player_image = $result['player_image'];
        $player_evaluation = $result['player_evaluation']; // Récupérer la valeur de player_evaluation
    }
}

// CRUD - Create
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    // Vérifier si le formulaire de création a été soumis
    $sql = "INSERT INTO player (player_name, player_firstname, player_birthday, player_club, player_height, player_weight, player_origin, player_favorite_foot, player_position, player_favorite_position, player_percentage_position, player_date_expiration_contract, player_agence, player_contact_agence, player_national_team, player_market_value, player_image, player_evaluation)
    VALUES ('{$_POST["player_name"]}', '{$_POST["player_firstname"]}', '{$_POST["player_birthday"]}', '{$_POST["player_club"]}', '{$_POST["player_height"]}', '{$_POST["player_weight"]}', '{$_POST["player_origin"]}', '{$_POST["player_favorite_foot"]}', '{$_POST["player_position"]}', '{$_POST["player_favorite_position"]}', '{$_POST["player_percentage_position"]}', '{$_POST["player_date_expiration_contract"]}', '{$_POST["player_agence"]}', '{$_POST["player_contact_agence"]}', '{$_POST["player_national_team"]}', '{$_POST["player_market_value"]}', '{$_POST["player_image"]}', '{$_POST["player_evaluation"]}')";
    // Requête SQL pour insérer un nouvel enregistrement dans la table avec les valeurs soumises dans le formulaire

    if ($db->query($sql) === TRUE) {
        echo "New record created successfully"; // Afficher un message de succès si l'insertion réussit
    } else {
        echo "Error: " . $sql . "<br>" . $db->errorInfo()[2]; // Afficher un message d'erreur si l'insertion échoue
    }
}

// CRUD - Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    // Vérifier si le formulaire de mise à jour a été soumis
    $sql = "UPDATE player SET player_name='{$_POST["player_name"]}', player_firstname='{$_POST["player_firstname"]}', player_birthday='{$_POST["player_birthday"]}', player_club='{$_POST["player_club"]}', player_height='{$_POST["player_height"]}', player_weight='{$_POST["player_weight"]}', player_origin='{$_POST["player_origin"]}', player_favorite_foot='{$_POST["player_favorite_foot"]}', player_position='{$_POST["player_position"]}', player_favorite_position='{$_POST["player_favorite_position"]}', player_percentage_position='{$_POST["player_percentage_position"]}', player_date_expiration_contract='{$_POST["player_date_expiration_contract"]}', player_agence='{$_POST["player_agence"]}', player_contact_agence='{$_POST["player_contact_agence"]}', player_national_team='{$_POST["player_national_team"]}', player_market_value='{$_POST["player_market_value"]}', player_image='{$_POST["player_image"]}', player_evaluation='{$_POST["player_evaluation"]}' WHERE player_id={$_POST["player_id"]}";
    // Requête SQL pour mettre à jour un enregistrement dans la table avec les nouvelles valeurs soumises dans le formulaire

    if ($db->query($sql) === TRUE) {
        echo "Record updated successfully"; // Afficher un message de succès si la mise à jour réussit
    } else {
        echo "Error: " . $sql . "<br>" . $db->errorInfo()[2]; // Afficher un message d'erreur si la mise à jour échoue
    }
    header( "Location: /Accueil/index.php" );
}
$db = null; // Fermer la connexion à la base de données


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/crud.css">
    <title>Document</title>
</head>
<body>
    <h2>Formulaire Joueur</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . ($player_id !== null ? '?player_id=' . $player_id : ''); ?>">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
        <label for="player_name">Nom :</label>
        <input type="text" id="player_name" name="player_name" value="<?php echo $player_name; ?>" required>

        <label for="player_firstname">Prénom :</label>
        <input type="text" id="player_firstname" name="player_firstname" value="<?php echo $player_firstname; ?>" required>

        <label for="player_birthday">Date de naissance :</label>
        <input type="date" id="player_birthday" name="player_birthday" value="<?php echo $player_birthday; ?>" required>

        <label for="player_club">Club :</label>
        <input type="text" id="player_club" name="player_club" value="<?php echo $player_club; ?>" required>

        <label for="player_height">Taille :</label>
        <input type="text" id="player_height" name="player_height" value="<?php echo $player_height; ?>" required>

        <label for="player_weight">Poids :</label>
        <input type="text" id="player_weight" name="player_weight" value="<?php echo $player_weight; ?>" required>

        <label for="player_origin">Origine :</label>
        <input type="text" id="player_origin" name="player_origin" value="<?php echo $player_origin; ?>" required>

        <label for="player_favorite_foot">Pied favori :</label>
        <input type="text" id="player_favorite_foot" name="player_favorite_foot" value="<?php echo $player_favorite_foot; ?>" required>

        <label for="player_position">Position :</label>
        <input type="text" id="player_position" name="player_position" value="<?php echo $player_position; ?>" required>

        <label for="player_favorite_position">Position favorite :</label>
        <input type="text" id="player_favorite_position" name="player_favorite_position" value="<?php echo $player_favorite_position; ?>" required>

        <label for="player_percentage_position">Pourcentage position :</label>
        <input type="text" id="player_percentage_position" name="player_percentage_position" value="<?php echo $player_percentage_position; ?>" required>

        <label for="player_date_expiration_contract">Date d'expiration du contrat :</label>
        <input type="date" id="player_date_expiration_contract" name="player_date_expiration_contract" value="<?php echo $player_date_expiration_contract; ?>" required>

        <label for="player_agence">Agence :</label>
        <input type="text" id="player_agence" name="player_agence" value="<?php echo $player_agence; ?>" required>

        <label for="player_contact_agence">Contact agence :</label>
        <input type="text" id="player_contact_agence" name="player_contact_agence" value="<?php echo $player_contact_agence; ?>" required>

        <label for="player_national_team">Équipe nationale :</label>
        <input type="text" id="player_national_team" name="player_national_team" value="<?php echo $player_national_team; ?>" required>

        <label for="player_market_value">Valeur marchande :</label>
        <input type="text" id="player_market_value" name="player_market_value" value="<?php echo $player_market_value; ?>" required>

        <label for="player_image">Image :</label>
        <input type="text" id="player_image" name="player_image" value="<?php echo $player_image; ?>" required>

        <label for="player_evaluation">Évaluation :</label>
        <select id="player_evaluation" name="player_evaluation" required>
            <option value="A" <?php echo ($player_evaluation == 'A') ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo ($player_evaluation == 'B') ? 'selected' : ''; ?>>B</option>
            <option value="C" <?php echo ($player_evaluation == 'C') ? 'selected' : ''; ?>>C</option>
            <option value="D" <?php echo ($player_evaluation == 'D') ? 'selected' : ''; ?>>D</option>
        </select>

        <input type="submit" name="<?php echo $player_id !== null ? 'update' : 'create'; ?>" value="<?php echo $player_id !== null ? 'Mettre à jour' : 'Créer'; ?>">
    </form>
</body>
</html>
