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
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif

        if (count($result) > 0) {
            $row = $result[0]; // Récupérer la première ligne du résultat
            if (password_verify($password, $row["users_password"])) {
                session_start();
                $_SESSION['user_connected'] = "ok";
                $_SESSION['user_name'] = $row["users_name"];
                header("Location: ../Player/index.php");
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
<!-- login.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Page de connexion</title>
</head>

<body>
    <h1>Connexion</h1>

    <?php
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les données du formulaire
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $db->prepare("SELECT users_id, users_password FROM users WHERE users_login = ?");
        $stmt->execute();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["users_password"];

            // Vérifier le mot de passe
            if (password_verify($password, $hashedPassword)) {
                // Identifiants corrects, démarrer la session
                session_start();
                $_SESSION["user_connected"] = "ok";
                header("Location: ../Player/index.php");
                exit();
            } else {
                $error_message = "Identifiants incorrects.";
            }
        } else {
            $error_message = "Identifiants incorrects.";
        }
    }
    ?>

    <?php if (isset($error_message)) { ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Nom d'utilisateur : <input type="text" name="username"><br>
        Mot de passe : <input type="password" name="password"><br>
        <input type="submit" value="Se connecter">
    </form>
</body>

</html>