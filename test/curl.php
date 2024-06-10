<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$curl = curl_init();

curl_setopt_array(
    $curl,
    array(
        CURLOPT_URL => 'https://apirest.wyscout.com/v3/players/1017517/advancedstats?compId=198',
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
        echo $response;
    }
}

curl_close($curl);