<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Fonction pour convertir une date au format jour/mois/année ou année-mois-jour en format standard
function convertDate($date)
{
    $dayMonthYearFormat = '/^(\d{2})\/(\d{2})\/(\d{4})$/';
    $yearMonthDayFormat = '/^(\d{4})-(\d{2})-(\d{2})$/';

    if (preg_match($dayMonthYearFormat, $date, $matches)) {
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    } elseif (preg_match($yearMonthDayFormat, $date, $matches)) {
        return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    } else {
        return null;
    }
}

// Nombre de joueurs par page
$playersPerPage = 20;

// Page actuelle
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Construire la requête SQL dynamique pour compter le nombre total de joueurs
$countSql = "SELECT COUNT(*) AS total FROM player";
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
    $contract_expiration = convertDate($_GET['contract_expiration']);
    if ($contract_expiration !== null) {
        $conditions[] = "player.player_date_expiration_contract = '$contract_expiration'";
    }
}

if (!empty($conditions)) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}

// Exécuter la requête SQL pour compter le nombre total de joueurs
$countStmt = $db->prepare($countSql);
$countStmt->execute();
$totalPlayers = $countStmt->fetchColumn();

// Calculer le nombre total de pages
$totalPages = ceil($totalPlayers / $playersPerPage);

// Construire la requête SQL dynamique pour récupérer les joueurs de la page actuelle
$sql = "SELECT player.player_id, player.player_name, player.player_firstname, player.player_position, player.player_club,
               YEAR(CURDATE()) - YEAR(player.player_birthday) AS player_age
        FROM player";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY player.player_name ASC";
$sql .= " LIMIT " . (($currentPage - 1) * $playersPerPage) . ", $playersPerPage";

// Exécuter la requête SQL
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../css/style.css">
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
                <input type="text" id="name" name="name"
                    value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
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
                        $selected = (isset($_GET['position']) && $_GET['position'] == $position) ? 'selected' : '';
                        echo "<option value='$position' $selected>$position</option>";
                    }
                    ?>
                </select>
                <label for="age">Âge :</label>
                <select id="age" name="age">
                    <option value="">Tous les âges</option>
                    <?php
                    // Générer les options pour la liste déroulante des âges
                    for ($age = 16; $age <= 40; $age++) {
                        $selected = (isset($_GET['age']) && $_GET['age'] == $age) ? 'selected' : '';
                        echo "<option value='$age' $selected>$age</option>";
                    }
                    ?>
                </select>
                <label for="contract_expiration">Date d'expiration du contrat (jj/mm/aaaa ou aaaa-mm-jj) :</label>
                <input type="text" id="contract_expiration" name="contract_expiration"
                    placeholder="jj/mm/aaaa ou aaaa-mm-jj"
                    value="<?php echo isset($_GET['contract_expiration']) ? htmlspecialchars($_GET['contract_expiration']) : ''; ?>">
                <input type="submit" value="Rechercher">
            </form>
        </div>

        <div class="listPlayer">
            <?php
            // Afficher les résultats dans un tableau HTML
            if (count($result) > 0) {
                echo "<table>";
                echo "<tr><th>Nom</th><th>Prénom</th><th>Poste</th><th>Club</th><th>Âge</th></tr>";
                foreach ($result as $row) {
                    echo "<tr>";
                    echo "<td><a href='../Player/index.php?player_id=" . $row['player_id'] . "'>" . htmlspecialchars($row['player_name']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['player_firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_position']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_club']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['player_age']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                // Afficher les liens de pagination
                echo '<div class="pagination">';
                $queryString = http_build_query(array_merge($_GET, array('page' => null)));
                for ($page = 1; $page <= $totalPages; $page++) {
                    $pageQueryString = $queryString . ($queryString ? '&' : '') . 'page=' . $page;
                    echo '<a href="?' . $pageQueryString . '" ' . ($page == $currentPage ? 'class="active"' : '') . '>' . $page . '</a>';
                }
                echo '</div>';
            } else {
                echo "Aucun joueur trouvé.";
            }
            ?>
        </div>
    </div>
</body>

</html>