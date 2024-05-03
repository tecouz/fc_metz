<?php
$dbhost = "localhost"; // ou votre adresse IP
$username = "root"; //nom d'utilisateur
$password = ""; // MDP
$dbname = "fc_metz"; // nom de la base de donnée

try {
    $db = new PDO("mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8", $username, $password);
} catch (Exception $e) {
    die("Erreur :" . $e->getMessage());
}