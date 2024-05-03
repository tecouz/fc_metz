<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>

<body>
    <header>
        <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <p>Accueil</p>
        <p>Gestion des scouts</p>
    </header>

    <div class="containerPage">
        <div class="searchPlayer">
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="name">Nom ou Prénom :</label>
                <input type="text" id="name" name="name">
                <label for="position">Poste :</label>
                <select id="position" name="position">
                    <option value="">Tous les postes</option>
                    <?php
                    // Récupérer les postes distincts depuis la base de données
                    $stmt = $db->prepare("SELECT DISTINCT player_position FROM player");
                    $stmt->execute();
                    $positions = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // Générer les options pour la liste déroulante des postes
                    foreach ($positions as $position) {
                        echo "<option value='$position'>$position</option>";
                    }
                    ?>
                </select>
                <label for="age">Âge :</label>
                <select id="age" name="age">
                    <option value="">Tous les âges</option>
                    <?php
                    // Générer les options pour la liste déroulante des âges
                    for ($age = 16; $age <= 40; $age++) {
                        echo "<option value='$age'>$age</option>";
                    }
                    ?>
                </select>
                <label for="contract_expiration">Date d'expiration du contrat :</label>
                <input type="date" id="contract_expiration" name="contract_expiration">
                <input type="submit" value="Rechercher">
            </form>
        </div>

        <div class="listPlayer">
            <?php
            // Construire la requête SQL dynamique
            $sql = "SELECT player.player_name, player.player_firstname, player.player_position, player.player_club,
                           YEAR(CURDATE()) - YEAR(player.player_birthday) AS player_age
                    FROM player";

            $conditions = array();

            if (!empty($_GET['name'])) {
                $name = $_GET['name'];
                $conditions[] = "(player.player_name LIKE '%$name%' OR player.player_firstname LIKE '%$name%')";
            }

            if (!empty($_GET['position'])) {
                $position = $_GET['position'];
                $conditions[] = "player.player_position = '$position'";
            }

            if (!empty($_GET['age'])) {
                $age = $_GET['age'];
                $conditions[] = "YEAR(CURDATE()) - YEAR(player.player_birthday) = $age";
            }

            if (!empty($_GET['contract_expiration'])) {
                $contract_expiration = $_GET['contract_expiration'];
                $conditions[] = "player.player_date_expiration_contract = '$contract_expiration'";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            // Exécuter la requête SQL
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Afficher les résultats dans un tableau HTML
            if (count($result) > 0) {
                echo "<table>";
                echo "<tr><th>Nom</th><th>Prénom</th><th>Poste</th><th>Club</th><th>Âge</th></tr>";
                foreach ($result as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['player_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_position']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_club']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_age']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Aucun joueur trouvé.";
            }
            ?>
        </div>
    </div>
</body>

</html>