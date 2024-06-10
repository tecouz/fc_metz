<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function getPlayerPosition($data)
{
    $position = '';
    if (isset($data['positions'][0]['position']['name'])) {
        $playerPosition = strtolower($data['positions'][0]['position']['name']);
        if (strpos($playerPosition, 'attaquant') !== false) {
            $position = 'attaquant';
        } elseif (strpos($playerPosition, 'defenseur') !== false || strpos($playerPosition, 'back') !== false) {
            $position = 'defenseur';
        } elseif (strpos($playerPosition, 'milieu') !== false) {
            $position = 'milieu';
        }
    }
    return $position;
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

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://apirest.wyscout.com/v3/players/1017517/advancedstats?compId=198', // Remplacez 1017517 par l'ID du joueur souhaité
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

                        // Récupérer la position du joueur à partir des données de l'API
                        $position = getPlayerPosition($data);

                        echo '<h2>' . ucfirst($position) . 's</h2>';
                        echo '<div class="container">';
                        if ($position === 'attaquant') {
                            foreach ($attaquants as $key => $value) {
                                echo "<div class='carte'>";
                                echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                echo "<p>" . $value . "</p>";
                                echo "</div>";
                            }
                        } elseif ($position === 'defenseur') {
                            foreach ($defenseurs as $key => $value) {
                                echo "<div class='carte'>";
                                echo "<h3>" . str_replace('_', ' ', $key) . "</h3>";
                                echo "<p>" . $value . "</p>";
                                echo "</div>";
                            }
                        } elseif ($position === 'milieu') {
                            foreach ($milieux as $key => $value) {
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

            curl_close($curl);
            ?>
        </div>
    </div>
</body>

</html>