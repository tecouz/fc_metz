<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

// Fonction pour convertir une date au format année-mois-jour en format standard
function convertDate($date)
{
    $yearMonthDayFormat = '/^(\d{4})-(\d{2})-(\d{2})$/';

    if (preg_match($yearMonthDayFormat, $date, $matches)) {
        return $date;
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
$playersPerPage = 20;

// Page actuelle, si le paramètre 'page' est présent dans l'URL, utiliser sa valeur, sinon utiliser 1
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Liste fixe des compétitions avec leurs identifiants
$competitions = [
    'Allemagne (1ère division)' => '426',
    'Autriche' => '168',
    'Belgique (1ère division)' => '198',
    'Danemark' => '335',
    'Espagne (1ère division)' => '795',
    'Espagne (2ème division)' => '797',
    'Hongrie' => '465',
    'Italie' => '524',
    'Norvège' => '669',
    'Pologne' => '692',
    'Portugal (1ère division)' => '707',
    'Rep. Tchèque' => '323',
    'Slovaquie' => '775',
    'Slovénie' => '776',
    'Suède' => '808',
    'Suisse' => '830',
    'Turquie (1ère division)' => '852'
];

// URL de base pour l'API Wyscout
$baseUrl = '';

// Paramètres de tri
$sortParams = array();

// Compétition
if (!empty($_GET['competition'])) {
    $competitionId = $_GET['competition']; // Utilisez l'ID directement depuis le paramètre GET
    $baseUrl = "https://apirest.wyscout.com/v3/competitions/$competitionId/players";
}

// Âge
if (!empty($_GET['age'])) {
    $sortParams['age'] = urlencode($_GET['age']);
}

// Poste
if (!empty($_GET['position'])) {
    $roleCode = getRoleCode($_GET['position']);
    if ($roleCode !== null) {
        $sortParams['roleCode'] = urlencode($roleCode);
    }
}

// Date d'expiration du contrat
if (!empty($_GET['contract_expiration'])) {
    $contract_expiration = convertDate($_GET['contract_expiration']);
    if ($contract_expiration !== null) {
        $sortParams['contractExpirationDate'] = urlencode($contract_expiration);
    }
}

// Nom ou prénom
if (!empty($_GET['name'])) {
    $sortParams['name'] = urlencode($_GET['name']);
}

// Fonction pour récupérer le code de rôle à partir du nom du poste
function getRoleCode($positionName)
{
    $roleMapping = array(
        'attaquant' => 'FW',
        'milieu' => 'MD',
        'defenseur' => 'DF',
        'gardien' => 'GK'
    );

    return isset($roleMapping[strtolower($positionName)]) ? $roleMapping[strtolower($positionName)] : null;
}

// Fonction pour récupérer les données paginées de l'API WyScout
function getPaginatedData($baseUrl, $params = [])
{
    $nextPattern = '/(?<=<)([\S]*)(?=>; rel="next")/i';
    $pagesRemaining = true;
    $data = [];

    $url = $baseUrl . '?' . http_build_query($params);

    while ($pagesRemaining) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg=='
            ],
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $linkHeader = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
        curl_close($curl);

        if ($httpCode >= 200 && $httpCode < 300) {
            $data = array_merge($data, json_decode($response, true)['players']);
            $pagesRemaining = preg_match($nextPattern, $linkHeader, $matches);
            if ($pagesRemaining) {
                $url = $matches[0];
            }
        } else {
            // Gérer les erreurs de requête ici
            break;
        }
    }

    return $data;
}

// Récupérer les données paginées de l'API WyScout
$params = array_merge(['limit' => $playersPerPage, 'page' => $currentPage], $sortParams);
$players = getPaginatedData($baseUrl, $params);

// Filtrer les joueurs en fonction des critères de recherche
$filteredPlayers = array_filter($players, function ($player) {
    $matchesFilters = true;

    if (!empty($_GET['name'])) {
        $name = $_GET['name'];
        $matchesFilters = $matchesFilters && (
            stripos($player['shortName'], $name) !== false ||
            stripos($player['firstName'], $name) !== false
        );
    }

    if (!empty($_GET['position'])) {
        $position = $_GET['position'];
        $roleCode = getRoleCode($position);
        $matchesFilters = $matchesFilters && (
            isset($player['role']['code2']) &&
            $player['role']['code2'] === $roleCode
        );
    }

    if (!empty($_GET['age'])) {
        $age = $_GET['age'];
        $playerBirthDate = $player['birthDate'];
        if ($playerBirthDate) {
            $birthDate = new DateTime($playerBirthDate);
            $now = new DateTime();
            $playerAge = $now->diff($birthDate)->y;
            $matchesFilters = $matchesFilters && ($playerAge == $age);
        }
    }

    if (!empty($_GET['contract_expiration'])) {
        $contractExpiration = $_GET['contract_expiration'];
        $playerContractExpirationDate = isset($player['contractExpirationDate']) ? $player['contractExpirationDate'] : '';
        if ($playerContractExpirationDate) {
            $matchesFilters = $matchesFilters && ($playerContractExpirationDate <= $contractExpiration);
        }
    }

    return $matchesFilters;
});

//Calculer le nombre total de joueurs après le filtrage
$totalPlayersAfterFiltering = count($filteredPlayers);

// Calculer le nombre total de pages
$totalPages = ceil($totalPlayersAfterFiltering / $playersPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/accueil.css">
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
                <label for="competition">Compétition :</label>
                <select id="competition" name="competition">
                    <option value="">Toutes les compétitions</option>
                    <?php
                    // Boucle sur les compétitions récupérées depuis l'API
                    foreach ($competitions as $competitionName => $competitionId) {
                        $selected = isset($_GET['competition']) && $_GET['competition'] == $competitionId ? 'selected' : '';
                        echo "<option value='$competitionId' $selected>$competitionName</option>";
                    }
                    ?>
                </select>

                <!-- Champs de tri avec la classe 'sorting-field' pour contrôler leur visibilité -->
                <div class="sorting-field" style="display: none;">
                    <label for="name">Nom ou Prénom :</label>
                    <input type="text" id="name" name="name"
                        value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
                </div>

                <div class="sorting-field" style="display: none;">
                    <label for="position">Poste :</label>
                    <select id="position" name="position">
                        <option value="">Tous les postes</option>
                        <option value="attaquant"
                            <?php echo isset($_GET['position']) && $_GET['position'] == 'attaquant' ? 'selected' : ''; ?>>
                            Attaquant</option>
                        <option value="milieu"
                            <?php echo isset($_GET['position']) && $_GET['position'] == 'milieu' ? 'selected' : ''; ?>>
                            Milieu</option>
                        <option value="defenseur"
                            <?php echo isset($_GET['position']) && $_GET['position'] == 'defenseur' ? 'selected' : ''; ?>>
                            Défenseur</option>
                        <option value="gardien"
                            <?php echo isset($_GET['position']) && $_GET['position'] == 'gardien' ? 'selected' : ''; ?>>
                            Gardien</option>
                    </select>
                </div>

                <div class="sorting-field" style="display: none;">
                    <label for="age">Âge :</label>
                    <select id="age" name="age">
                        <option value="">Tous les âges</option>
                        <?php
                        // Générer les options pour la liste déroulante des âges
                        for ($age = 16; $age <= 40; $age++) {
                            $selected = isset($_GET['age']) && $_GET['age'] == $age ? 'selected' : '';
                            echo "<option value='$age' $selected>$age</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="sorting-field" style="display: none;">
                    <label for="contract_expiration">Date d'expiration du contrat (jj/mm/aaaa ou aaaa-mm-jj) :</label>
                    <input type="text" id="contract_expiration" name="contract_expiration"
                        placeholder="jj/mm/aaaa ou aaaa-mm-jj"
                        value="<?php echo isset($_GET['contract_expiration']) ? htmlspecialchars($_GET['contract_expiration']) : ''; ?>">
                </div>

                <input type="submit" value="Rechercher">
            </form>
        </div>

        <div class="listPlayer">
            <?php
            // Afficher les résultats dans un tableau HTML
            if (count($filteredPlayers) > 0) {
                echo "<table>";
                echo "<tr><th>Nom</th><th>Prénom</th><th>Poste</th><th>Club</th><th>Âge</th><th>Évaluation</th><th>Nationalité</th><th>Pied fort</th><th>Taille</th><th>Poids</th></tr>";

                $rowCount = 0;

                foreach ($filteredPlayers as $player) {
                    $rowClass = ($rowCount % 2 == 0) ? 'row-even' : 'row-odd'; // Classe CSS pour alterner les couleurs de ligne
                    echo "<tr class='$rowClass'>";

                    // Nom (shortName)
                    $playerShortName = array_key_exists('shortName', $player) ? htmlspecialchars($player['shortName']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerShortName . "</a></td>";

                    // Prénom
                    $playerFirstName = array_key_exists('firstName', $player) ? htmlspecialchars($player['firstName']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerFirstName . "</a></td>";

                    // Poste
                    $playerPosition = array_key_exists('role', $player) && is_array($player['role']) && array_key_exists('name', $player['role']) ? htmlspecialchars($player['role']['name']) : '';
                    echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerPosition . "</a></td>";

                                        // Club (obtenu via l'URL de Teams)
                                        $playerClub = ''; // Initialiser $playerClub avec une chaîne vide
                                        $teamUrl = 'https://apirest.wyscout.com/v3/teams/' . $player['currentTeamId'];
                                        $teamCurl = curl_init();
                                        curl_setopt_array($teamCurl, array(
                                            CURLOPT_URL => $teamUrl,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'GET',
                                            CURLOPT_HTTPHEADER => array('Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg==')
                                        ));
                                        curl_setopt($teamCurl, CURLOPT_SSL_VERIFYPEER, false);
                                        $teamResponse = curl_exec($teamCurl);
                                        curl_close($teamCurl);
                                        $teamData = json_decode($teamResponse, true);
                                        $playerClub = array_key_exists('name', $teamData) ? htmlspecialchars($teamData['name']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerClub . "</a></td>";
                    
                                        // Âge
                                        $playerBirthDate = array_key_exists('birthDate', $player) ? $player['birthDate'] : '';
                                        if (!empty($playerBirthDate)) {
                                            $birthDate = new DateTime($playerBirthDate);
                                            $now = new DateTime();
                                            $age = $now->diff($birthDate)->y;
                                            echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $age . "</a></td>";
                                        } else {
                                            echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">-</a></td>"; // Afficher un tiret si la date de naissance n'est pas disponible
                                        }
                    
                                        // Évaluation
                                        $playerEvaluation = array_key_exists('evaluation', $player) ? htmlspecialchars($player['evaluation']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\" style='background-color: " . getBackgroundColor($playerEvaluation) . ";'>" . $playerEvaluation . "</a></td>";
                    
                                        // Nationalité
                                        $playerNationality = array_key_exists('passportArea', $player) && is_array($player['passportArea']) && array_key_exists('name', $player['passportArea']) ? htmlspecialchars($player['passportArea']['name']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerNationality . "</a></td>";
                    
                                        // Pied fort
                                        $playerFoot = array_key_exists('foot', $player) && isset($player['foot']) ? htmlspecialchars($player['foot']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerFoot . "</a></td>";
                    
                                        // Taille
                                        $playerHeight = array_key_exists('height', $player) ? htmlspecialchars($player['height']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerHeight . "</a></td>";
                    
                                        // Poids
                                        $playerWeight = array_key_exists('weight', $player) ? htmlspecialchars($player['weight']) : '';
                                        echo "<td><a href='../Player/index.php?player_id=" . $player['wyId'] . "&competition_id=" . $competitionId . "' onclick=\"setCookies('" . $player['wyId'] . "', '" . $competitionId . "'); return true;\">" . $playerWeight . "</a></td>";
                    
                                        echo "</tr>";
                                        $rowCount++;
                                    }
                                    echo "</table>";
                    
                                    // Construire la chaîne de requête sans le paramètre 'page'
                                    $queryString = http_build_query(array_merge($_GET, array('page' => null)));
                    
                                    // Afficher les liens de pagination
                                    echo '<div class="pagination">';
                    
                                    // Bouton "Précédent"
                                    if ($currentPage > 1) {
                                        $prevPage = $currentPage - 1;
                                        $prevQueryString = $queryString . ($queryString ? '&' : '') . 'page=' . $prevPage;
                                        echo '<a href="?' . $prevQueryString . '" class="prev-button">Précédent</a>';
                                    }
                    
                                    // Liens de pagination
                                    for ($page = 1; $page <= $totalPages; $page++) {
                                        $pageQueryString = $queryString . ($queryString ? '&' : '') . 'page=' . $page;
                                        echo '<a href="?' . $pageQueryString . '" ' . ($page == $currentPage ? 'class="active"' : '') . '>' . $page . '</a>';
                                    }
                    
                                    // Bouton "Suivant"
                                    if ($currentPage < 50) {
                                        $nextPage = $currentPage + 1;
                                        $nextQueryString = $queryString . ($queryString ? '&' : '') . 'page=' . $nextPage;
                                        echo '<a href="?' . $nextQueryString . '" class="next-button">Suivant</a>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo "Aucun joueur trouvé.";
                                }
                                ?>
        </div>
    </div>
    <script>
    function setCookies(playerId, competitionId) {
        // Définir les cookies
        document.cookie = "player_id=" + playerId + "; path=/";
        document.cookie = "competition_id=" + competitionId + "; path=/";
    }
    </script>
    <script src="../js/filter.js"></script>
</body>

</html>