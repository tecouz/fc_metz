<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Récupérer l'ID du joueur et l'ID de la compétition à partir des cookies
$player_id = isset($_COOKIE['player_id']) ? $_COOKIE['player_id'] : null;
$competition_id = isset($_COOKIE['competition_id']) ? $_COOKIE['competition_id'] : null;

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    <div class="containerPage">
        <div>
            <h1>Statistique Spécifiques Saison 2023/2024 du joueur</h1>
            <p>Statistique mises à jour après chaque match</p>
        </div>

        <div class="container">
            <?php
            $curl = curl_init();

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

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $player_response = curl_exec($curl);

            if ($player_response === false) {
                echo 'Erreur cURL : ' . curl_error($curl);
            } else {
                $player_data = json_decode($player_response, true);

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

                    $response = curl_exec($curl);

                    if ($response === false) {
                        echo 'Erreur cURL : ' . curl_error($curl);
                    } else {
                        if (empty($response)) {
                            echo 'Réponse vide';
                        } else {
                            $data = json_decode($response, true);

                            // Boucle d'affichage des résultats
                            if (!empty($data)) {
                                $attaquants = [
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    'goals' => isset($data['total']['goals']) ? $data['total']['goals'] : '0',
                                    'goals_per_90' => isset($data['average']['goals']) ? $data['average']['goals'] : '0',
                                    'penalties' => isset($data['total']['penalties']) ? $data['total']['penalties'] : '0',
                                    'assists' => isset($data['total']['assists']) ? $data['total']['assists'] : '0',
                                    'assists_per_90' => isset($data['average']['assists']) ? $data['average']['assists'] : '0',
                                    'xg_shot_per_90' => isset($data['average']['xgShot']) ? $data['average']['xgShot'] : '0',
                                    'shots_per_90' => isset($data['average']['shots']) ? $data['average']['shots'] : '0',
                                    'shots_on_target_per_90' => isset($data['average']['shotsOnTarget']) ? $data['average']['shotsOnTarget'] : '0',
                                    'shots_on_target_percentage_per_90' => isset($data['percent']['shotsOnTarget']) ? $data['percent']['shotsOnTarget'] : '0',
                                    'dribbles_per_90' => isset($data['average']['dribbles']) ? $data['average']['dribbles'] : '0',
                                    'dribbles_success_percentage_per_90' => isset($data['percent']['successfulDribbles']) ? $data['percent']['successfulDribbles'] : '0',
                                    'offensive_duels_per_90' => isset($data['average']['offensiveDuels']) ? $data['average']['offensiveDuels'] : '0',
                                    'offensive_duels_won_percentage_per_90' => isset($data['percent']['offensiveDuelsWon']) ? $data['percent']['offensiveDuelsWon'] : '0',
                                    'aerial_duels_per_90' => isset($data['average']['aerialDuels']) ? $data['average']['aerialDuels'] : '0',
                                    'aerial_duels_won_percentage_per_90' => isset($data['percent']['aerialDuelsWon']) ? $data['percent']['aerialDuelsWon'] : '0'
                                ];

                                $defenseurs = [
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    'goals' => isset($data['total']['goals']) ? $data['total']['goals'] : '0',
                                    'goals_per_90' => isset($data['average']['goals']) ? $data['average']['goals'] : '0',
                                    'assists' => isset($data['total']['assists']) ? $data['total']['assists'] : '0',
                                    'assists_per_90' => isset($data['average']['assists']) ? $data['average']['assists'] : '0',
                                    'interceptions_per_90' => isset($data['average']['interceptions']) ? $data['average']['interceptions'] : '0',
                                    'sliding_tackles_per_90' => isset($data['average']['slidingTackles']) ? $data['average']['slidingTackles'] : '0',
                                    'aerial_duels_per_90' => isset($data['average']['aerialDuels']) ? $data['average']['aerialDuels'] : '0',
                                    'aerial_duels_won_percentage_per_90' => isset($data['percent']['aerialDuelsWon']) ? $data['percent']['aerialDuelsWon'] : '0',
                                    'defensive_duels_per_90' => isset($data['average']['defensiveDuels']) ? $data['average']['defensiveDuels'] : '0',
                                    'defensive_duels_won_percentage_per_90' => isset($data['percent']['defensiveDuelsWon']) ? $data['percent']['defensiveDuelsWon'] : '0',
                                    'defensive_actions_per_90' => isset($data['average']['defensiveActions']) ? $data['average']['defensiveActions'] : '0',
                                    'long_passes_completed_per_90' => isset($data['average']['successfulLongPasses']) ? $data['average']['successfulLongPasses'] : '0',
                                    'passes_received_per_90' => isset($data['average']['receivedPass']) ? $data['average']['receivedPass'] : '0',
                                    'progressive_runs_per_90' => isset($data['average']['progressiveRun']) ? $data['average']['progressiveRun'] : '0',
                                    'dribbles_completed_per_90' => isset($data['average']['successfulDribbles']) ? $data['average']['successfulDribbles'] : '0',
                                    'second_assists_per_90' => isset($data['average']['secondAssists']) ? $data['average']['secondAssists'] : '0',
                                    'smart_passes_per_90' => isset($data['average']['smartPasses']) ? $data['average']['smartPasses'] : '0',
                                    'direct_passes_per_90' => isset($data['average']['directPasses']) ? $data['average']['directPasses'] : '0',
                                    'progressive_passes_per_90' => isset($data['average']['progressivePasses']) ? $data['average']['progressivePasses'] : '0',
                                    'passes_to_final_third_per_90' => isset($data['average']['passesToFinalThird']) ? $data['average']['passesToFinalThird'] : '0'
                                ];

                                $milieux = [
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    'goals' => isset($data['total']['goals']) ? $data['total']['goals'] : '0',
                                    'goals_per_90' => isset($data['average']['goals']) ? $data['average']['goals'] : '0',
                                    'assists' => isset($data['total']['assists']) ? $data['total']['assists'] : '0',
                                    'assists_per_90' => isset($data['average']['assists']) ? $data['average']['assists'] : '0',
                                    'dribbles_attempted_per_90' => isset($data['average']['dribbles']) ? $data['average']['dribbles'] : '0',
                                    'dribbles_success_percentage_per_90' => isset($data['percent']['successfulDribbles']) ? $data['percent']['successfulDribbles'] : '0',
                                    'offensive_duels_per_90' => isset($data['average']['offensiveDuels']) ? $data['average']['offensiveDuels'] : '0',
                                    'offensive_duels_won_percentage_per_90' => isset($data['percent']['offensiveDuelsWon']) ? $data['percent']['offensiveDuelsWon'] : '0',
                                    'fouls_suffered_per_90' => isset($data['average']['foulsSuffered']) ? $data['average']['foulsSuffered'] : '0',
                                    'shots_on_target_per_90' => isset($data['average']['shotsOnTarget']) ? $data['average']['shotsOnTarget'] : '0',
                                    'passes_received_per_90' => isset($data['average']['receivedPass']) ? $data['average']['receivedPass'] : '0',
                                    'progressive_runs_per_90' => isset($data['average']['progressiveRun']) ? $data['average']['progressiveRun'] : '0',
                                    'second_assists_per_90' => isset($data['average']['secondAssists']) ? $data['average']['secondAssists'] : '0',
                                    'key_passes_per_90' => isset($data['average']['keyPasses']) ? $data['average']['keyPasses'] : '0',
                                    'smart_passes_per_90' => isset($data['average']['smartPasses']) ? $data['average']['smartPasses'] : '0',
                                    'direct_passes_per_90' => isset($data['average']['directPasses']) ? $data['average']['directPasses'] : '0',
                                    'progressive_passes_per_90' => isset($data['average']['progressivePasses']) ? $data['average']['progressivePasses'] : '0',
                                    'passes_to_final_third_per_90' => isset($data['average']['passesToFinalThird']) ? $data['average']['passesToFinalThird'] : '0'
                                ];

                                $gardiens = [
                                    'matches_played' => isset($data['total']['matches']) ? $data['total']['matches'] : '0',
                                    'starts' => isset($data['total']['matchesInStart']) ? $data['total']['matchesInStart'] : '0',
                                    'minutes_played' => isset($data['total']['minutesOnField']) ? $data['total']['minutesOnField'] : '0',
                                    'clean_sheets' => isset($data['total']['gkCleanSheets']) ? $data['total']['gkCleanSheets'] : '0',
                                    'goals_conceded' => isset($data['total']['gkConcededGoals']) ? $data['total']['gkConcededGoals'] : '0',
                                    'shots_faced' => isset($data['total']['gkShotsAgainst']) ? $data['total']['gkShotsAgainst'] : '0',
                                    'saves' => isset($data['total']['gkSaves']) ? $data['total']['gkSaves'] : '0',
                                    'save_percentage' => isset($data['percent']['gkSaves']) ? $data['percent']['gkSaves'] : '0',
                                    'exits' => isset($data['total']['gkExits']) ? $data['total']['gkExits'] : '0',
                                    'successful_exits' => isset($data['total']['gkSuccessfulExits']) ? $data['total']['gkSuccessfulExits'] : '0',
                                    'successful_exits_percentage' => isset($data['percent']['gkSuccessfulExits']) ? $data['percent']['gkSuccessfulExits'] : '0',
                                    'aerial_duels' => isset($data['total']['gkAerialDuels']) ? $data['total']['gkAerialDuels'] : '0',
                                    'aerial_duels_won' => isset($data['total']['gkAerialDuelsWon']) ? $data['total']['gkAerialDuelsWon'] : '0',
                                    'aerial_duels_won_percentage' => isset($data['percent']['gkAerialDuelsWon']) ? $data['percent']['gkAerialDuelsWon'] : '0'
                                ];

                                echo '<h2>' . ucfirst($role) . 's</h2>';
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
            ?>
        </div>
    </div>
</body>

</html>