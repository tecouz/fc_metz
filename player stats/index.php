<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

// Récupérer l'ID du joueur et l'ID de la compétition à partir des cookies
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;
$competition_id = isset($_COOKIE['competition_id']) ? $_COOKIE['competition_id'] : null;

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Activer l'affichage des erreurs PHP

function getPlayerRole($data)
{
    if (isset($data['role']['code2'])) {
        $roleCode = $data['role']['code2'];
        switch ($roleCode) {
            case 'FW':
                return 'attaquant';
            case 'DF':
                return 'defenseur';
            case 'MD':
                return 'milieu';
            case 'GK':
                return 'gardien';
            default:
                return '';
        }
    }
    return '';
}
// Fonction pour récupérer le rôle du joueur à partir du code de rôle fourni par l'API
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistique</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Mitr:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/card.css">
</head>

<body>
    <?php include "../Nav/nav.php"; ?>
    <!-- Inclure le fichier de navigation -->

    <div class="containerPage">
        <div>
            <h1>Statistique Spécifiques Saison 2023/2024 du joueur</h1>
            <p>Statistique mises à jour après chaque match</p>
        </div>

        <div class="container">
            <?php
            $curl = curl_init();
            // Initialiser une nouvelle session cURL

            // Récupérer les informations du joueur
            $player_url = 'https://apirest.wyscout.com/v3/players/' . $player_id . '?imageDataURL=1';
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $player_url,
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
            // Configurer les options cURL pour récupérer les informations du joueur depuis l'API

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $player_response = curl_exec($curl);
            // Exécuter la requête cURL et récupérer la réponse

            if ($player_response === false) {
                echo 'Erreur cURL : ' . curl_error($curl);
            } else {
                $player_data = json_decode($player_response, true);
                // Décoder la réponse JSON en un tableau associatif PHP

                if (!empty($player_data)) {
                    // Récupérer le rôle du joueur
                    $role = getPlayerRole($player_data);

                    // Récupérer les statistiques du joueur
                    curl_setopt_array(
                        $curl,
                        array(
                            CURLOPT_URL => 'https://apirest.wyscout.com/v3/players/' . $player_id . '/advancedstats?compId=' . $competition_id,
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
                    // Configurer les options cURL pour récupérer les statistiques du joueur depuis l'API

                    $response = curl_exec($curl);
                    // Exécuter la requête cURL et récupérer la réponse

                    if ($response === false) {
                        echo 'Erreur cURL : ' . curl_error($curl);
                    } else {
                        if (empty($response)) {
                            echo 'Réponse vide';
                        } else {
                            $data = json_decode($response, true);
                            // Décoder la réponse JSON en un tableau associatif PHP

                            // Boucle d'affichage des résultats
                            if (!empty($data)) {
                                $attaquants = [
                                    // Tableau associatif contenant les statistiques pour les attaquants
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    // ... (autres statistiques pour les attaquants)
                                ];

                                $defenseurs = [
                                    // Tableau associatif contenant les statistiques pour les défenseurs
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    // ... (autres statistiques pour les défenseurs)
                                ];

                                $milieux = [
                                    // Tableau associatif contenant les statistiques pour les milieux de terrain
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    // ... (autres statistiques pour les milieux de terrain)
                                ];

                                $gardiens = [
                                    // Tableau associatif contenant les statistiques pour les gardiens de but
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    // ... (autres statistiques pour les gardiens de but)
                                ];

                                echo '<h2>' . ucfirst($role) . 's</h2>';
                                // Afficher le titre correspondant au rôle du joueur

                                echo '<div class="container">';

                                if ($role === 'attaquant') {
                                    foreach ($attaquants as $key => $value) {
                                        echo "<div class='carte'>";
                                        echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                        echo "<p>" . $value . "</p>";
                                        echo "</div>";
                                    }
                                } elseif ($role === 'defenseur') {
                                    foreach ($defenseurs as $key => $value) {
                                        echo "<div class='carte'>";
                                        echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                        echo "<p>" . $value . "</p>";
                                        echo "</div>";
                                    }
                                } elseif ($role === 'milieu') {
                                    foreach ($milieux as $key => $value) {
                                        echo "<div class='carte'>";
                                        echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                        echo "<p>" . $value . "</p>";
                                        echo "</div>";
                                    }
                                } elseif ($role === 'gardien') {
                                    foreach ($gardiens as $key => $value) {
                                        echo "<div class='carte'>";
                                        echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                        echo "<p>" . $value . "</p>";
                                        echo "</div>";
                                    }
                                }

                                echo '</div>';
                            } else {
                                echo "Aucune statistique trouvée";
                            }
                        }
                    }
                } else {
                    echo "Aucune information de joueur trouvée";
                }
            }

            curl_close($curl);
            // Fermer la session cURL
            ?>
        </div>
    </div>
</body>

</html>