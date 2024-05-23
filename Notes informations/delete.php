<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $note_id = $_GET['id'];

    // Vérifier si l'utilisateur connecté a le droit de supprimer cette note
    $sql = "SELECT users_id FROM note WHERE note_id = :note_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":note_id", $note_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['users_id'] == $_SESSION['user_id']) {
        // L'utilisateur connecté est autorisé à supprimer cette note
        $sql = "DELETE FROM note WHERE note_id = :note_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":note_id", $note_id);
        $stmt->execute();
    } else {
        echo "Vous n'êtes pas autorisé à supprimer cette note.";
    }
}

header("Location: ../Notes informations/index.php");
exit;