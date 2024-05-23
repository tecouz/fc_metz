<?php
// Inclure le fichier de connexion à la base de données
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de protection pour vérifier si l'utilisateur est connecté
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Fonction pour convertir une date au format jour/mois/année ou année-mois-jour en format standard
function convertDate($date)
{
    // Expressions régulières pour les formats de date acceptés
    $dayMonthYearFormat = '/^(\d{2})\/(\d{2})\/(\d{4})$/';
    $yearMonthDayFormat = '/^(\d{4})-(\d{2})-(\d{2})$/';

    // Vérifier si la date correspond au format jour/mois/année
    if (preg_match($dayMonthYearFormat, $date, $matches)) {
        // Retourner la date au format standard année-mois-jour
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }
    // Vérifier si la date correspond au format année-mois-jour
    elseif (preg_match($yearMonthDayFormat, $date, $matches)) {
        // Retourner la date telle quelle (déjà au format standard)
        return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    } else {
        // Si le format de la date n'est pas valide, retourner null
        return null;
    }
}

// Fonction pour déterminer la couleur de fond en fonction de l'évaluation
function getBackgroundColor($evaluation)
{
    switch ($evaluation) {
        case 'A':
            return 'green';
        case 'B':
            return '#FAA40F';
        case 'C':
            return '#FB6107';
        case 'D':
            return 'red';
        default:
            return 'transparent';
    }
}

// Nombre de joueurs à afficher par page
$playersPerPage = 20;

// Page actuelle, si le paramètre 'page' est présent dans l'URL, utiliser sa valeur, sinon utiliser 1
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Construire la requête SQL dynamique pour compter le nombre total de joueurs
$countSql = "SELECT COUNT(*) AS total FROM player";
$conditions = array(); // Tableau pour stocker les conditions de filtrage

// Si le paramètre 'name' est présent dans l'URL, ajouter une condition de filtrage sur le nom ou le prénom
if (!empty($_GET['name'])) {
    $name = $_GET['name'];
    $conditions[] = "(player.player_name LIKE '%$name%' OR player.player_firstname LIKE '%$name%')";
}

// Si le paramètre 'position' est présent dans l'URL, ajouter une condition de filtrage sur le poste
if (!empty($_GET['position'])) {
    $position = $_GET['position'];
    $conditions[] = "player.player_position = '$position'";
}

// Si le paramètre 'age' est présent dans l'URL, ajouter une condition de filtrage sur l'âge
if (!empty($_GET['age'])) {
    $age = $_GET['age'];
    $conditions[] = "YEAR(CURDATE()) - YEAR(player.player_birthday) = $age";
}

// Si le paramètre 'contract_expiration' est présent dans l'URL, ajouter une condition de filtrage sur la date d'expiration du contrat
if (!empty($_GET['contract_expiration'])) {
    $contract_expiration = convertDate($_GET['contract_expiration']);
    if ($contract_expiration !== null) {
        $conditions[] = "player.player_date_expiration_contract = '$contract_expiration'";
    }
}

// Si le tableau $conditions n'est pas vide, ajouter les conditions de filtrage à la requête SQL
if (!empty($conditions)) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}

// Préparer et exécuter la requête SQL pour compter le nombre total de joueurs
$countStmt = $db->prepare($countSql);
$countStmt->execute();
$totalPlayers = $countStmt->fetchColumn(); // Récupérer le nombre total de joueurs

// Calculer le nombre total de pages en divisant le nombre total de joueurs par le nombre de joueurs par page
$totalPages = ceil($totalPlayers / $playersPerPage);

// Construire la requête SQL dynamique pour récupérer les joueurs de la page actuelle
$sql = "SELECT player.player_id, player.player_name, player.player_firstname, player.player_position, player.player_club,
               YEAR(CURDATE()) - YEAR(player.player_birthday) AS player_age, player.player_evaluation
        FROM player";

// Si le tableau $conditions n'est pas vide, ajouter les conditions de filtrage à la requête SQL
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Trier les résultats par nom de joueur
$sql .= " ORDER BY player.player_name ASC";
// Limiter les résultats à la page actuelle
$sql .= " LIMIT " . (($currentPage - 1) * $playersPerPage) . ", $playersPerPage";

// Préparer et exécuter la requête SQL pour récupérer les joueurs de la page actuelle
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="..\css\style.css">
    <link rel="stylesheet" href="..\css\accueil.css">
</head>

<body>
    <header>
        <!-- Afficher le nom de l'utilisateur connecté -->
        <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <a href="../Accueil/index.php" class="button">Accueil</a>
        <a href="../Scout/index.php" class="button">Gestion des scouts</a>
    </header>

    <div class="containerPage">
        <div class="searchPlayer">
            <!-- Formulaire de recherche avec les filtres -->
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
                echo "<tr><th>Nom</th><th>Prénom</th><th>Poste</th><th>Club</th><th>Âge</th><th>Évaluation</th></tr>";
                $rowCount = 0;
                foreach ($result as $row) {
                    $rowClass = ($rowCount % 2 == 0) ? 'row-even' : 'row-odd'; // Classe CSS pour alterner les couleurs de ligne
                    echo "<tr class='$rowClass'>";
                    // Lien vers la page du joueur en cliquant sur les cellules
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\">" . htmlspecialchars($row['player_name']) . "</td>";
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\">" . htmlspecialchars($row['player_firstname']) . "</td>";
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\">" . htmlspecialchars($row['player_position']) . "</td>";
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\">" . htmlspecialchars($row['player_club']) . "</td>";
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\">" . htmlspecialchars($row['player_age']) . "</td>";
                    echo "<td onclick=\"window.location.href='../Player/index.php?player_id=" . $row['player_id'] . "'\" style=\"background-color: " . getBackgroundColor($row['player_evaluation']) . ";\">" . htmlspecialchars($row['player_evaluation']) . "</td>"; // Affichage de la valeur de player_evaluation avec couleur de fond
                    echo "</tr>";
                    $rowCount++;
                }
                echo "</table>";

                // Afficher les liens de pagination
                echo '<div class="pagination">';
                $queryString = http_build_query(array_merge($_GET, array('page' => null))); // Construire la chaîne de requête sans le paramètre 'page'
                for ($page = 1; $page <= $totalPages; $page++) {
                    $pageQueryString = $queryString . ($queryString ? '&' : '') . 'page=' . $page; // Ajouter le paramètre 'page' à la chaîne de requête
                    echo '<a href="?' . $pageQueryString . '" ' . ($page == $currentPage ? 'class="active"' : '') . '>' . $page . '</a>'; // Lien de pagination avec classe CSS active pour la page actuelle
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