<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Requête SQL pour récupérer les utilisateurs avec le rôle "scout"
$sql = "SELECT u.users_id, u.users_name, u.users_firstname
        FROM users u
        INNER JOIN role_users ru ON u.users_id = ru.users_id
        INNER JOIN role r ON ru.role_id = r.role_id
        WHERE r.role_id = 2"; // 2 est l'ID du rôle "scout"

$stmt = $db->prepare($sql);
$stmt->execute();
$scouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Renvoyer la liste des scouts au format JSON
header('Content-Type: application/json');
echo json_encode($scouts);