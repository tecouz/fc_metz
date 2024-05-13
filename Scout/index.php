<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

// Définir la page actuelle
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Requête SQL pour récupérer les utilisateurs avec le rôle "scout"
$sql = "SELECT u.users_name, u.users_firstname
        FROM users u
        INNER JOIN role_users ru ON u.users_id = ru.users_id
        INNER JOIN role r ON ru.role_id = r.role_id
        WHERE r.role_id = 2"; // 2 est l'ID du rôle "scout"

$stmt = $db->prepare($sql);
$stmt->execute();
$scouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si la requête est une requête POST avec l'action "add_event"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_event') {
    $eventTitle = $_POST['eventTitle'];
    $eventDescription = $_POST['eventDescription'];
    $eventDate = $_POST['eventDate'];
    $eventStartTime = $_POST['eventStartTime'] ? $_POST['eventStartTime'] : null;
    $eventEndTime = $_POST['eventEndTime'] ? $_POST['eventEndTime'] : null;
    $eventLocation = $_POST['eventLocation'];
    $eventParticipants = $_POST['eventParticipants'];

    // Préparer et exécuter la requête SQL pour insérer un nouvel événement
    $stmt = $db->prepare('INSERT INTO evenement (evenement_title, evenement_description, evenement_date, evenement_start_time, evenement_end_time, evenement_location, evenement_participants) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bindParam(1, $eventTitle, PDO::PARAM_STR);
    $stmt->bindParam(2, $eventDescription, PDO::PARAM_STR);
    $stmt->bindParam(3, $eventDate, PDO::PARAM_STR);
    $stmt->bindParam(4, $eventStartTime, PDO::PARAM_STR);
    $stmt->bindParam(5, $eventEndTime, PDO::PARAM_STR);
    $stmt->bindParam(6, $eventLocation, PDO::PARAM_STR);
    $stmt->bindParam(7, $eventParticipants, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "L'événement a été ajouté avec succès.";
        // Rediriger vers la même page pour éviter la résoumission des données
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=2');
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'ajout de l'événement.";
    }
} else {
    echo "Requête invalide.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des scouts</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/scout.css">
</head>

<body>
    <header>
        <!-- Afficher le nom de l'utilisateur connecté -->
        <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <a href="../Accueil/index.php" class="button">Accueil</a>
        <a href="../Scout/index.php" class="button">Gestion des scouts</a>
    </header>

    <div class="pagination">
        <a href="?page=1" class="<?php echo ($currentPage == 1) ? 'active' : ''; ?>">Liste des scouts</a>
        <a href="?page=2" class="<?php echo ($currentPage == 2) ? 'active' : ''; ?>">Calendrier</a>
    </div>

    <?php
    // Afficher le contenu de la page en fonction de la page actuelle
    if ($currentPage == 1) {
        // Code pour afficher la liste des scouts
        echo '<h2>Liste des scouts</h2>';
        if (count($scouts) > 0): ?>
            <ul>
                <?php foreach ($scouts as $scout): ?>
                    <li><?php echo $scout['users_name'] . ' ' . $scout['users_firstname']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun scout trouvé.</p>
        <?php endif;
    } elseif ($currentPage == 2) {
        // Code pour afficher le calendrier
        echo '<h2>Calendrier</h2>';
        ?>
        <label for="monthSelect">Mois :</label>
        <select id="monthSelect"></select>

        <label for="yearSelect">Année :</label>
        <select id="yearSelect"></select>

        <table id="calendar"></table>

        <div id="eventDetails" style="display: none;">
            <h2>Détails de l'événement</h2>
            <form id="eventForm" method="post">
                <input type="hidden" name="action" value="add_event">
                <input type="hidden" id="eventDate" name="eventDate">
                <label for="eventTitle">Titre :</label>
                <input type="text" id="eventTitle" name="eventTitle" required>
                <br>
                <label for="eventDescription">Description :</label>
                <textarea id="eventDescription" name="eventDescription"></textarea>
                <br>
                <label for="eventStartTime">Heure de début :</label>
                <input type="time" id="eventStartTime" name="eventStartTime">
                <br>
                <label for="eventEndTime">Heure de fin :</label>
                <input type="time" id="eventEndTime" name="eventEndTime">
                <br>
                <label for="eventLocation">Lieu :</label>
                <input type="text" id="eventLocation" name="eventLocation">
                <br>
                <label for="eventParticipants">Participants :</label>
                <textarea id="eventParticipants" name="eventParticipants"></textarea>
                <br>
                <button type="submit">Ajouter</button>
                <button type="button" id="cancelEvent">Annuler</button>
            </form>
        </div>

        <script src="../js/calendar.js"></script>

    <?php } ?>
</body>

</html>