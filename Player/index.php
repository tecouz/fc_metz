<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Récupérer l'ID du joueur et l'ID de la compétition à partir de l'URL
$player_id = isset($_GET['player_id']) ? $_GET['player_id'] : null;
$competition_id = isset($_GET['competition_id']) ? $_GET['competition_id'] : null;

// Stocker l'ID du joueur et l'ID de la compétition dans des cookies
if ($player_id !== null) {
    setcookie('player_id', $player_id, time() + (86400 * 30), "/"); // Expire dans 30 jours
}
if ($competition_id !== null) {
    setcookie('competition_id', $competition_id, time() + (86400 * 30), "/"); // Expire dans 30 jours
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
        <div class="player-header">
            <h1>Informations du joueur</h1>
            <?php if ($player_id !== null) : ?>
            <a href="../CRUD/process.php?player_id=<?php echo $player_id; ?>" class="edit-button">Modifier</a>
            <?php endif; ?>
        </div>

        <div class="container">
            <?php
            // Vérifier si l'ID du joueur est présent
            if ($player_id !== null) {
                $curl = curl_init();

                // Récupérer les informations du joueur depuis l'API WyScout
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
                        echo '<div class="container">';

                        // Récupérer la date d'expiration du contrat depuis l'API WyScout
                        $contract_info_url = 'https://apirest.wyscout.com/v3/players/' . $player_id . '/contractinfo';
                        $contract_info_response = @file_get_contents($contract_info_url, false, stream_context_create(array(
                            'http' => array(
                                'header' => "Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg==\r\n"
                            )
                        )));

                        if ($contract_info_response !== false) {
                            $contract_info = json_decode($contract_info_response, true);
                            $contract_expiration_date = isset($contract_info['contractExpirationDate']) ? $contract_info['contractExpirationDate'] : '';

                            // Afficher la carte de la date d'expiration du contrat uniquement si la date n'est pas vide
                            if (!empty($contract_expiration_date)) {
                                echo "<div class='carte'>";
                                echo "<h3>Date d'expiration du contrat</h3>";
                                echo "<p>$contract_expiration_date</p>";
                                echo "</div>";
                            }
                        }

                        foreach ($player_data as $key => $value) {
                            // Exclure les clés non désirées
                            if ($key !== 'wyId' && $key !== 'imageDataURL' && $key !== 'shortName' && $key !== 'middleName' && $key !== 'gender' && $key !== 'passportArea') {
                                // Vérifier si la valeur est vide ou null
                                if (!empty($value) && $value !== null) {
                                    echo "<div class='carte'>";
                                    echo "<h3>" . ucwords(str_replace('_', ' ', $key)) . "</h3>";

                                    // Vérifier si la valeur est un tableau
                                    if (is_array($value)) {
                                        // Cas spécial pour les clés 'role', 'birthArea' et 'passportArea'
                                        if ($key === 'role') {
                                            $roleName = isset($value['name']) ? htmlspecialchars($value['name']) : '';
                                            echo "<p>$roleName</p>";
                                        } elseif ($key === 'birthArea') {
                                            $areaName = isset($value['name']) ? htmlspecialchars($value['name']) : '';
                                            echo "<p>$areaName</p>";
                                        } else {
                                            echo "<pre>" . print_r($value, true) . "</pre>";
                                        }
                                    } elseif ($key === 'currentTeamId') {
                                        // Récupérer le nom de l'équipe depuis l'API WyScout
                                        $team_url = 'https://apirest.wyscout.com/v3/teams/' . $value;
                                        $team_response = @file_get_contents($team_url, false, stream_context_create(array(
                                            'http' => array(
                                                'header' => "Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg==\r\n"
                                            )
                                        )));
                                        if ($team_response !== false) {
                                            $team_data = json_decode($team_response, true);
                                            $team_name = isset($team_data['name']) ? htmlspecialchars($team_data['name']) : '';
                                            echo "<p>$team_name</p>";
                                        }
                                    } elseif ($key === 'currentNationalTeamId') {
                                        // Récupérer le nom de l'équipe nationale depuis l'API WyScout
                                        $national_team_url = 'https://apirest.wyscout.com/v3/teams/' . $value;
                                        $national_team_response = @file_get_contents($national_team_url, false, stream_context_create(array(
                                            'http' => array(
                                                'header' => "Authorization: Basic cmM4ajZiai15ZnM1czAyZW4tcnBkamtyai1ndHRuZ2lodW8wOiEyOVJMUHZFK283aWhOOlRCKigpWiE3JUpzLm5NUg==\r\n"
                                            )
                                        )));
                                        if ($national_team_response !== false) {
                                            $national_team_data = json_decode($national_team_response, true);
                                            $national_team_name = isset($national_team_data['name']) ? htmlspecialchars($national_team_data['name']) : '';
                                            echo "<p>$national_team_name</p>";
                                        }
                                    } else {
                                        // Échappement HTML
                                        $value = htmlspecialchars($value);
                                        echo "<p>$value</p>";
                                    }

                                    echo "</div>";
                                }
                            }
                        }
                        echo '</div>';
                    } else {
                        echo "Aucune information trouvée pour ce joueur";
                    }
                }

                curl_close($curl);
            } else {
                echo "Aucun joueur sélectionné";
            }
            ?>
        </div>
    </div>
</body>

</html>