<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="./CSS/style.css"/>
    <link rel="stylesheet" href="./CSS/style.form.css"/>

</head>
<body>
    <div class = "container">
        <img src="./images/eau_login.jpg"/>
        <form class="form-login" action="PHPScripts/login.php" method="post">
            <h3>Connectez vous</h3>
            <div class="input-field">
                Pseudo : <input name="pseudo" type="text">
            </div>
            <div class="input-field"> 
                Mot de passe : <input name="mdp" type="password"> 
            </div>
            <div class="submit-wrapper">
                <button class="submit" type= "submit">Se connecter</button>
            </div>
            <p class="error-message" style="display:<?php echo isset($_GET["error"]) ? "block" : "none"?>;" >
            <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "champVide") {
                    echo "Veuillez renseigner tous les champs";
                }
                else if ($_GET["error"] == "informationserronees")  {
                    echo "Votre pseudo ou votre mot de passe est incorrect";
                }
                else if ($_GET["error"] == "erreurBD") {
                   echo "Quelque chose n'a pas marché. Veuillez réessayer";
                }
                else {
                    echo "Une erreur inconnue est survenue";
                 }
            }
            ?>
            </p>

            <a href="signup.page.php">Pas encore de compte ? Inscrivez-vous ici</a>
        </form>
    </div>
</body>
</html>