<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

$event_id = 0;
$event_title = "";
$event_description = "";
$event_date = "";
$event_start_time = "";
$event_end_time = "";
$event_location = "";
$event_participants = array();

// Récupérer la liste des scouts
$sql = "SELECT u.users_name, u.users_firstname
        FROM users u
        INNER JOIN role_users ru ON u.users_id = ru.users_id
        INNER JOIN role r ON ru.role_id = r.role_id
        WHERE r.role_id = 2"; // 2 est l'ID du rôle "scout"

$stmt = $db->prepare($sql);
$stmt->execute();
$scouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $sql = "SELECT * FROM evenement WHERE evenement_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([":id" => $_GET['id']]);
    if ($row = $stmt->fetch()) {
        $event_id = $_GET['id'];
        $event_title = $row["evenement_title"];
        $event_description = $row["evenement_description"];
        $event_date = $row["evenement_date"];
        $event_start_time = $row["evenement_start_time"];
        $event_end_time = $row["evenement_end_time"];
        $event_location = $row["evenement_location"];
        $event_participants = explode(',', $row["evenement_participants"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Événement</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/fontawesome-free/css/all.css">
</head>

<body>
    <main class="container">
        <a href="index.php" title="Retour">
            <i class="fa-solid fa-arrow-left mt-3"></i>
            Retour
        </a>
        <form action="process.php" method="post">
            <div class="form-group mt-3">
                <label for="event_title">Titre :</label>
                <input type="text" class="form-control" name="event_title" id="event_title"
                    placeholder="Entrez le titre de l'événement" value="<?= $event_title; ?>">
            </div>

            <div class="form-group mt-3">
                <label for="event_description">Description :</label>
                <textarea class="form-control" name="event_description" id="event_description"
                    placeholder="Entrez la description de l'événement"><?= $event_description; ?></textarea>
            </div>

            <div class="form-group mt-3">
                <label for="event_date">Date :</label>
                <input type="date" class="form-control" name="event_date" id="event_date" value="<?= $event_date; ?>">
            </div>

            <div class="form-group mt-3">
                <label for="event_start_time">Heure de début :</label>
                <input type="time" class="form-control" name="event_start_time" id="event_start_time"
                    value="<?= $event_start_time; ?>">
            </div>

            <div class="form-group mt-3">
                <label for="event_end_time">Heure de fin :</label>
                <input type="time" class="form-control" name="event_end_time" id="event_end_time"
                    value="<?= $event_end_time; ?>">
            </div>

            <div class="form-group mt-3">
                <label for="event_location">Lieu :</label>
                <input type="text" class="form-control" name="event_location" id="event_location"
                    placeholder="Entrez le lieu de l'événement" value="<?= $event_location; ?>">
            </div>

            <div class="form-group mt-3">
                <label for="event_participants">Participants :</label>
                <select class="form-control" name="event_participants" id="event_participants" multiple>
                    <?php foreach ($scouts as $scout) : ?>
                    <option value="<?= $scout['users_name'] . ' ' . $scout['users_firstname']; ?>"
                        <?php if (in_array($scout['users_name'] . ' ' . $scout['users_firstname'], $event_participants)) echo 'selected'; ?>>
                        <?= $scout['users_name'] . ' ' . $scout['users_firstname']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Valider</button>
            <input type="hidden" name="event_id" value="<?= $event_id; ?>">
        </form>
    </main>
</body>

</html>