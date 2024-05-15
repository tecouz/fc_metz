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


function getEventsForMonth($month, $year)
{
    global $db; // Utiliser la variable $db pour accéder à la connexion à la base de données

    // Préparer la requête SQL pour récupérer les événements du mois spécifié
    $stmt = $db->prepare("SELECT * FROM evenement WHERE MONTH(evenement_date) = ? AND YEAR(evenement_date) = ?");
    $stmt->bindParam(1, $month, PDO::PARAM_INT);
    $stmt->bindParam(2, $year, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $events;
}


// Définir le mois et l'année actuels
$today = new DateTime();
$currentMonth = $today->format('n'); // Mois actuel (1-12)
$currentYear = $today->format('Y'); // Année actuelle

// Récupérer les événements du mois en cours
$events = getEventsForMonth($currentMonth, $currentYear);
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
        ?>
        <div class="container">
            <div>
                <label for="monthSelect">Mois :</label>
                <select id="monthSelect"></select>
                <label for="yearSelect">Année :</label>
                <select id="yearSelect"></select>
            </div>
            <div id="addEvntBtn">Ajouter un événement</div>
            <div id="calendar"></div>
            <div id="eventList">
                <h3>Événements du mois</h3>
                <?php if (count($events) > 0) { ?>
                    <ul>
                        <?php foreach ($events as $event) { ?>
                            <li>
                                <strong><?php echo $event['evenement_title']; ?></strong>
                                <p><?php echo $event['evenement_description']; ?></p>
                                <p>Date : <?php echo date('d/m/Y', strtotime($event['evenement_date'])); ?></p>
                                <p>Heure de début : <?php echo $event['evenement_start_time']; ?></p>
                                <p>Heure de fin : <?php echo $event['evenement_end_time']; ?></p>
                                <p>Lieu : <?php echo $event['evenement_location']; ?></p>
                                <p>Participants : <?php echo $event['evenement_participants']; ?></p>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Aucun événement pour ce mois.</p>
                <?php } ?>
            </div>
        </div>
        <?php
    }
    ?>

    <script src="../js/calendar.js"></script>
</body>

</html>