<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

    <link rel="stylesheet" href="./CSS/style.css"/>
    <link rel="stylesheet" href="./CSS/style.form.css"/>

</head>
<body>
    <div class = "container">
        <img src="./images/eau_login.jpg"/>
        <form class="form-login" action="PHPScripts/signup.php" method="post">
            <h3>Créez un compte</h3>

            <div class="input-field">
                Pseudo :<input name="pseudo" type="text"> 
            </div>
            <div class="input-field">
                Mot de passe : <input name="mdp" type="password"> 
            </div>
            <div class="input-field">
                Confirmer le mot de passe : <input name="mdp-validation" type="password"> 
            </div>
            <div class = "submit-wrapper">    
                <button class="submit" type="submit">Créer</button>    
            </div>
            <p class="error-message" style="display:<?php echo isset($_GET["error"]) ? "block" : "none"?>;" >
            <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "champVide") {
                    echo "Veuillez renseigner tous les champs";
                }
                else if ($_GET["error"] == "pseudoInvalide")  {
                    echo "Votre pseudo ne peut être constitué que de caractères alphanumériques";
                }
                else if ($_GET["error"] == "pseudoExistant") {
                    echo "Ce pseudo à déjà été pris";
                }
                else if ($_GET["error"] == "mdpCourt") {
                    echo "Votre mot de passe doit faire au moins 8 caractères";
                }
                else if ($_GET["error"] == "mdpInequivalents") {
                    echo "Vous n'avez pas rentré le même mot de passe dans le champ de vérification";
                }
                else if ($_GET["error"] == "erreurBD") {
                    echo "Quelque chose n'a pas marché. Veuillez réessayer";
                }
            }
            ?>
            </p>

            <a href="login.page.php">Déjà un compte ? Connectez-vous ici</a>
        </form>
    </div>
</body>
</html>