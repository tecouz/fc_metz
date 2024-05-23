<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/copy.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Vidéo</title>
</head>

<body>
    <?php include "../Nav/nav.php"; ?>
    <div>
        <div class="containerPage">
            <p>observation vidéo</p>
            <a href="https://www.hudl.com" target="_blank">Hudl</a>

            <div class="card">
                <div class="card-body">
                    <div class="login-info">
                        <p><strong>Identifiant :</strong> IdentifiantFcM</p>
                        <p><strong>Mot de passe :</strong> <input type="password" id="password-input"
                                value="motdepasse123" readonly></p>
                    </div>
                    <div class="copy-buttons">
                        <button class="copy-btn" id="copy-username">
                            <i class="fa-solid fa-copy"></i> Copier l'identifiant
                        </button>
                        <button class="copy-btn" id="copy-password">
                            <i class="fa-solid fa-copy"></i> Copier le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/copy.js"></script>
</body>

</html>