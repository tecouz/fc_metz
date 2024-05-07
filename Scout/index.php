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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        <div id="eventFormContainer">
            <h2>Ajouter un événement</h2>
            <form id="eventForm">
                <label for="eventTitle">Titre :</label>
                <input type="text" id="eventTitle" name="eventTitle" required>
                <br>
                <label for="eventDate">Date :</label>
                <input type="date" id="eventDate" name="eventDate" required>
                <br>
                <button type="submit">Ajouter</button>
            </form>
        </div>
        <script src="../js/calendar.js"></script>

    <?php } ?>
</body>

</html>