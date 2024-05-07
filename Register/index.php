<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $users_name = htmlspecialchars($_POST['users_name']);
    $users_firstname = htmlspecialchars($_POST['users_firstname']);
    $users_login = htmlspecialchars($_POST['users_login']);
    $user_mail = htmlspecialchars($_POST['user_mail']); // Récupérer l'email
    $plainPassword = htmlspecialchars($_POST['users_password']);

    // Hasher le mot de passe
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Préparer et exécuter la requête SQL pour insérer un nouvel utilisateur
    $stmt = $db->prepare('INSERT INTO users (users_name, users_firstname, users_login, user_mail, users_password) VALUES (?, ?, ?, ?, ?)');
    $stmt->bindParam(1, $users_name, PDO::PARAM_STR);
    $stmt->bindParam(2, $users_firstname, PDO::PARAM_STR);
    $stmt->bindParam(3, $users_login, PDO::PARAM_STR);
    $stmt->bindParam(4, $user_mail, PDO::PARAM_STR); // Lier l'email
    $stmt->bindParam(5, $hashedPassword, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Rediriger vers une page de confirmation ou de connexion
        $redirectPath = $_SERVER["DOCUMENT_ROOT"] . "/Login/index.php";
        if (file_exists($redirectPath)) {
            header('Location: ../Login/index.php');
            exit;
        } else {
            echo "Le fichier de redirection n'existe pas.";
        }
    } else {
        echo "Une erreur s'est produite lors de l'insertion des données.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Inscription</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Mitr:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="..\css\login.css">
</head>

<body>
<div class="containerPage">
        <div class="CardLogin">
            <div class="CardLogin-Left">
                
               
            </div>
            <div class="CardLogin-Right">
   
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="users_name">Nom :</label>
        <input type="text" id="users_name" name="users_name" required><br>

        <label for="users_firstname">Prénom :</label>
        <input type="text" id="users_firstname" name="users_firstname" required><br>

        <label for="users_login">Nom d'utilisateur :</label>
        <input type="text" id="users_login" name="users_login" required><br>

        <label for="user_mail">Email :</label> <!-- Ajouter un champ pour l'email -->
        <input type="email" id="user_mail" name="user_mail" required><br>

        <label for="users_password">Mot de passe :</label>
        <input type="password" id="users_password" name="users_password" required><br>

        <button type="submit">S'inscrire</button>
    </form>
    </div>
</body>

</html>