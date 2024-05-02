<?php
// Connexion à la base de données
$servername = "localhost"; // ou votre adresse IP
$username = "root"; //nom d'utilisateur
$password = ""; // MDP
$database = "fc_metz"; // nom de la base de donnée

$conn = new mysqli($servername, $username, $password, $database); // requête SQL de connexion a la base de donnée

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $users_name = htmlspecialchars($_POST['users_name']);
    $users_firstname = htmlspecialchars($_POST['users_firstname']);
    $users_login = htmlspecialchars($_POST['users_login']);
    $plainPassword = htmlspecialchars($_POST['users_password']);

    // Hasher le mot de passe
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Préparer et exécuter la requête SQL pour insérer un nouvel utilisateur
    $stmt = $conn->prepare('INSERT INTO users (users_name, users_firstname, users_login, users_password) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $users_name, $users_firstname, $users_login, $hashedPassword);
    $stmt->execute();

    // Rediriger vers une page de confirmation ou de connexion
    header('Location: ../Login/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="users_name">Nom :</label>
        <input type="text" id="users_name" name="users_name" required><br>

        <label for="users_firstname">Prénom :</label>
        <input type="text" id="users_firstname" name="users_firstname" required><br>

        <label for="users_login">Nom d'utilisateur :</label>
        <input type="text" id="users_login" name="users_login" required><br>

        <label for="users_password">Mot de passe :</label>
        <input type="password" id="users_password" name="users_password" required><br>

        <input type="submit" value="S'inscrire">
    </form>
</body>

</html>