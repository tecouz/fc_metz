<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
// Inclure le fichier de connexion à la base de données

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";
// Inclure le fichier de protection (probablement pour la gestion des sessions ou des autorisations)

$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
// Récupérer la page actuelle à partir du paramètre GET 'page', ou définir à 1 par défaut

if (isset($_GET['month']) && isset($_GET['year']) && $_GET['page'] == 2) {
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);
    echo getEventsForMonth($month, $year);
    exit;
}
// Si les paramètres 'month' et 'year' sont présents dans l'URL et que la page est 2, afficher les événements pour le mois et l'année spécifiés

// Requête SQL pour récupérer les utilisateurs avec le rôle "scout"
$sql = "SELECT u.users_name, u.users_firstname
        FROM users u
        INNER JOIN role_users ru ON u.users_id = ru.users_id
        INNER JOIN role r ON ru.role_id = r.role_id
        WHERE r.role_id = 2"; // 2 est l'ID du rôle "scout"

$stmt = $db->prepare($sql);
$stmt->execute();
$scouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Récupérer la liste des utilisateurs ayant le rôle "scout" depuis la base de données

function getEventsForMonth($month, $year)
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM evenement WHERE MONTH(evenement_date) = ? AND YEAR(evenement_date) = ?");
    $stmt->bindParam(1, $month, PDO::PARAM_INT);
    $stmt->bindParam(2, $year, PDO::PARAM_INT);
    $stmt->execute();

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Générer le HTML pour afficher les événements
    $html = '';
    if (count($events) > 0) {
        $html .= '<table class="table table-striped border-2 border">';
        $html .= '<tr><th scope="col">Titre</th><th scope="col">Description</th><th scope="col">Date</th><th scope="col">Heure de début</th><th scope="col">Heure de fin</th><th scope="col">Lieu</th><th scope="col">Participants</th><th scope="col" class="text-center">Supprimer</th><th scope="col" class="text-center">Modifier</th></tr>';
        foreach ($events as $event) {
            $html .= '<tr>';
            $html .= '<td>' . $event['evenement_title'] . '</td>';
            $html .= '<td>' . $event['evenement_description'] . '</td>';
            $html .= '<td>' . date('d/m/Y', strtotime($event['evenement_date'])) . '</td>';
            $html .= '<td>' . $event['evenement_start_time'] . '</td>';
            $html .= '<td>' . $event['evenement_end_time'] . '</td>';
            $html .= '<td>' . $event['evenement_location'] . '</td>';
            $html .= '<td>' . $event['evenement_participants'] . '</td>';
            $html .= '<td class="text-center"><a href="delete.php?id=' . $event['evenement_id'] . '" title="Supprimer l\'événement"><i class="fa-solid fa-trash text-danger"></i></a></td>';
            $html .= '<td class="text-center"><a href="form.php?id=' . $event['evenement_id'] . '" title="Modifier l\'événement"><i class="fa-solid fa-pen-to-square"></i></a></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    } else {
        $html .= '<p>Aucun événement pour ce mois.</p>';
    }

    return $html;
}
// Fonction pour récupérer les événements d'un mois et d'une année donnés, et générer le HTML pour les afficher

$today = new DateTime();
$currentMonth = $today->format('n');
$currentYear = $today->format('Y');
// Récupérer le mois et l'année actuels
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des scouts</title>
    <link rel="stylesheet" href="../css/fontawesome-free/css/all.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/scout.css">
</head>

<body>
    <header>
        <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <a href="../Accueil/index.php" class="button">Accueil</a>
        <a href="../Scout/index.php" class="button">Gestion des scouts</a>
    </header>
    <main class="container">
        <div class="d-flex justify-content-between my-3 align-items-center">
            <h1>Gestion des scouts</h1>
        </div>
        <div class="pagination">
            <a href="?page=1" class="<?php echo ($currentPage == 1) ? 'active' : ''; ?>">Liste des scouts</a>
            <a href="?page=2" class="<?php echo ($currentPage == 2) ? 'active' : ''; ?>">Calendrier</a>
        </div>
        <?php
        if ($currentPage == 1) {
            echo '<h2>Liste des scouts</h2>';
            if (count($scouts) > 0): ?>
                <table class="table table-striped border-2 border">
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Prénom</th>
                    </tr>
                    <?php foreach ($scouts as $scout): ?>
                        <tr>
                            <td><?php echo $scout['users_name']; ?></td>
                            <td><?php echo $scout['users_firstname']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Aucun scout trouvé.</p>
            <?php endif;
        } elseif ($currentPage == 2) {
            $month = isset($_GET['month']) ? ($_GET['month']) : $currentMonth;
            $year = isset($_GET['year']) ? ($_GET['year']) : $currentYear;
            $events = getEventsForMonth($month, $year);
            ?>
            <div class="container">
                <div>
                    <label for="monthSelect">Mois :</label>
                    <select id="monthSelect"></select>
                    <label for="yearSelect">Année :</label>
                    <select id="yearSelect"></select>
                </div>
                <a href="form.php" id="addEvntBtn">Ajouter un événement</a>
                <div id="calendar"></div>
                <div id="eventList">
                    <?php echo $events; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </main>
    <script src="../js/calendar.js"></script>
</body>

</html>