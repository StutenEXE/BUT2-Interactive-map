<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="./CSS/style.css"/>
</head>
<body>
    <form action="PHPScripts/login.php" method="post">
        <input name="pseudo" type="text"> Pseudo
        <br>
        <input name="mdp" type="password"> Mot de passe     
        <button type= "submit"  value="soumettre"> Soumettre </button>
    </form>

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
        }
        ?>
    </p>

    <a href="signup.page.php">Pas encore de compte ? Inscirvez-vous ici</a>
</body>
</html>