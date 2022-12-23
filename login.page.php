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
    <div class = "corps">
        <form class="formLogin" action="PHPScripts/login.php" method="post">
            <h1>Connectez vous<h1>
            <h2>Pseudo : </h2>
            <br>
            <input name="pseudo" type="text"> 
            <br>
            <h2>Mot de passe :</h2>
            <br>
            <input name="mdp" type="password"> 
            <br>
            <div class="soumettre">
                <button class="loginButton" type= "submit"  value="soumettre">Se Connecter</button>
            </div>
            
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
    </div>
</body>
</html>