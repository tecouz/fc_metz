<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";

$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $stmt = $db->prepare("SELECT users_id, users_password, users_name FROM users WHERE users_login = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $row = $result[0];
            if (password_verify($password, $row["users_password"])) {
                session_start();
                $_SESSION['user_connected'] = "ok";
                $_SESSION['user_name'] = $row["users_name"];
                $_SESSION['user_id'] = $row["users_id"]; // Stocker l'ID de l'utilisateur dans la session

                header("Location: ../Accueil/index.php");
                exit();
            } else {
                $errorMessage = "Identifiants incorrects.";
            }
        } else {
            $errorMessage = "Identifiants incorrects.";
        }
    } else {
        $errorMessage = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Page de connexion</title>
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
                <?php if (!empty($errorMessage)) { ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php } ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" autocomplete="username"
                        placeholder="nom d'utilisateur" required>
                    <br> <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="mot de passe" required>
                    <br> <button type="submit">Se connecter</button>
                    <br><a href="..\Register\index.php">s'inscrire</p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>