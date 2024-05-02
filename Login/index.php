<!-- login.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Page de connexion</title>
</head>

<body>
    <h1>Connexion</h1>

    <?php
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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les données du formulaire
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT users_id, users_password FROM users WHERE users_login = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["users_password"]; // Supposons que le mot de passe haché est stocké dans la colonne "password_hash"
    
            // Vérifier le mot de passe
            if (password_verify($password, $hashedPassword)) {
                // Identifiants corrects, rediriger vers une page sécurisée
                session_start();
                $_SESSION["users_login"] = $username;
                header("Location: ../Player/index.php");
                exit();
            } else {
                $error_message = "Identifiants incorrects.";
            }
        } else {
            $error_message = "Identifiants incorrects.";
        }

        $stmt->close();
    }

    $conn->close();
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