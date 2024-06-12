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
$playersPerPage = 25;

// Page actuelle, si le paramètre 'page' est présent dans l'URL, utiliser sa valeur, sinon utiliser 1
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// URL de base pour l'API Wyscout
$baseUrl = 'https://apirest.wyscout.com/v3/competitions/198/players';

// Paramètres de tri
$sortParams = array();

if (!empty($_GET['competition'])) {
    $sortParams['competitionName'] = urlencode($_GET['competition']);
}

if (!empty($_GET['age'])) {
    $sortParams['age'] = urlencode($_GET['age']);
}

if (!empty($_GET['position'])) {
    $sortParams['positionName'] = urlencode($_GET['position']);
}

if (!empty($_GET['contract_expiration'])) {
    $contract_expiration = convertDate($_GET['contract_expiration']);
    if ($contract_expiration !== null) {
        $sortParams['contractExpirationDate'] = urlencode($contract_expiration);
    }
}

if (!empty($_GET['name'])) {
    $sortParams['name'] = urlencode($_GET['name']);
}

// Construire l'URL complète avec les paramètres de tri
$url = $baseUrl . '?' . http_build_query(array_merge(array('limit' => $playersPerPage, 'page' => $currentPage), $sortParams));

// Initialiser cURL
$curl = curl_init();

curl_setopt_array(
    $curl,
    array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg=='
        ),
    )
);

curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($curl);

if ($response === false) {
    echo 'Erreur cURL : ' . curl_error($curl);
} else {
    if (empty($response)) {
        echo 'Réponse vide';
    } else {
        $data = json_decode($response, true);
        $players = $data['players'];

        // Récupérer le nombre total de joueurs
        $totalPlayers = isset($data['pagination']['totalCount']) ? $data['pagination']['totalCount'] : 0;

        // Calculer le nombre total de pages
        $totalPages = ceil($totalPlayers / $playersPerPage);
    }
}

curl_close($curl);
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

                <label for="competition">Compétition :</label>
                <select id="competition" name="competition">
                    <option value="">Toutes les compétitions</option>
                    <?php
                    // Récupérer les compétitions distinctes depuis l'API
                    $competitionUrl = 'https://apirest.wyscout.com/v3/competitions';
                    $competitionCurl = curl_init();
                    curl_setopt_array(
                        $competitionCurl,
                        array(
                            CURLOPT_URL => $competitionUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg=='
                            ),
                        )
                    );
                    curl_setopt($competitionCurl, CURLOPT_SSL_VERIFYPEER, false);
                    $competitionResponse = curl_exec($competitionCurl);
                    curl_close($competitionCurl);

                    $competitionData = json_decode($competitionResponse, true);
                    foreach ($competitionData['competitions'] as $competition) {
                        $selected = (isset($_GET['competition']) && $_GET['competition'] == $competition['name']) ? 'selected' : '';
                        echo "<option value='" . $competition['name'] . "' $selected>" . $competition['name'] . "</option>";
                    }
                    ?>
                </select>
                <label for="position">Poste :</label>
                <select id="position" name="position">
                    <option value="">Tous les postes</option>
                    <?php
                    // Récupérer les postes distincts depuis l'API
                    $positionUrl = 'https://apirest.wyscout.com/v3/positions';
                    $positionCurl = curl_init();
                    curl_setopt_array(
                        $positionCurl,
                        array(
                            CURLOPT_URL => $positionUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg=='
                            ),
                        )
                    );
                    curl_setopt($positionCurl, CURLOPT_SSL_VERIFYPEER, false);
                    $positionResponse = curl_exec($positionCurl);
                    curl_close($positionCurl);

                    $positionData = json_decode($positionResponse, true);
                    foreach ($positionData['positions'] as $position) {
                        $selected = (isset($_GET['position']) && $_GET['position'] == $position['name']) ? 'selected' : '';
                        echo "<option value='" . $position['name'] . "' $selected>" . $position['name'] . "</option>";
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
            if (count($players) > 0) {
                echo "<table>";
                echo "<tr><th>Nom</th><th>Prénom</th><th>Poste</th><th>Club</th><th>Âge</th><th>Évaluation</th><th>Nationalité</th><th>Pied fort</th><th>Taille</th><th>Poids</th></tr>";
                $rowCount = 0;
                foreach ($players as $player) {
                    $rowClass = ($rowCount % 2 == 0) ? 'row-even' : 'row-odd'; // Classe CSS pour alterner les couleurs de ligne
                    echo "<tr class='$rowClass'>";

                    // Nom (shortName)
                    $playerShortName = array_key_exists('shortName', $player) ? htmlspecialchars($player['shortName']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerShortName . "</a></td>";

                    // Prénom
                    $playerFirstName = array_key_exists('firstName', $player) ? htmlspecialchars($player['firstName']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerFirstName . "</a></td>";

                    // Poste
                    $playerPosition = array_key_exists('role', $player) && is_array($player['role']) && array_key_exists('name', $player['role']) ? htmlspecialchars($player['role']['name']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerPosition . "</a></td>";

                    // Club (obtenu via l'URL de Teams)
                    $teamUrl = 'https://apirest.wyscout.com/v3/teams/' . $player['currentTeamId'];
                    $teamCurl = curl_init();
                    curl_setopt_array(
                        $teamCurl,
                        array(
                            CURLOPT_URL => $teamUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg=='
                            ),
                        )
                    );
                    curl_setopt($teamCurl, CURLOPT_SSL_VERIFYPEER, false);
                    $teamResponse = curl_exec($teamCurl);
                    curl_close($teamCurl);

                    $teamData = json_decode($teamResponse, true);
                    $playerClub = array_key_exists('name', $teamData) ? htmlspecialchars($teamData['name']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerClub . "</a></td>";

                    // Âge
                    $playerBirthDate = array_key_exists('birthDate', $player) ? $player['birthDate'] : '';
                    if (!empty($playerBirthDate)) {
                        $birthDate = new DateTime($playerBirthDate);
                        $now = new DateTime();
                        $age = $now->diff($birthDate)->y;
                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $age . "</a></td>";
                    } else {
                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>-</a></td>"; // Afficher un tiret si la date de naissance n'est pas disponible
                    }

                    // Évaluation
                    $playerEvaluation = array_key_exists('evaluation', $player) ? htmlspecialchars($player['evaluation']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "' style='background-color: " . getBackgroundColor($playerEvaluation) . ";'>" . $playerEvaluation . "</a></td>";

                    // Nationalité
                    $playerNationality = array_key_exists('passportArea', $player) && is_array($player['passportArea']) && array_key_exists('name', $player['passportArea']) ? htmlspecialchars($player['passportArea']['name']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerNationality . "</a></td>";

                    // Pied fort
                    $playerFoot = array_key_exists('foot', $player) && isset($player['foot']) ? htmlspecialchars($player['foot']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerFoot . "</a></td>";

                    // Taille
                    $playerHeight = array_key_exists('height', $player) ? htmlspecialchars($player['height']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerHeight . "</a></td>";

                    // Poids
                    $playerWeight = array_key_exists('weight', $player) ? htmlspecialchars($player['weight']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "'>" . $playerWeight . "</a></td>";

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