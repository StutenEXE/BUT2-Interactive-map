<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>
    <form action="PHPScripts/signup.php" method="post">
        <input name="pseudo" type="text"> Pseudo
        <br>
        <input name="mdp" type="password"> Mot de passe
        <br>
        <input name="mdp-validation" type="password"> Confirmer le mot de passe
        <button type="submit" value="soumettre"> Soumettre </button>    
    </form>

    <p class="error-message"><?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "champVide") {
                echo "Veuillez renseigner tous les champs";
            }
            else if ($_GET["error"] == "pseudoInvalide")  {
                echo "Votre pseudo ne peut être constitué que de caractères alphanumériques";
            }
            else if ($_GET["error"] == "pseudoExistant") {
                echo "Ce pseudo a déjà été pris";
            }
            else if ($_GET["error"] == "mdpCourt") {
                echo "Votre mot de passe doit faire au moins 8 caractères";
            }
            else if ($_GET["error"] == "mdpInequivalents") {
                echo "Vous n'avez pas rentré le même mot de passe dans le champ de vérification";
            }
        }
    ?></p>

    <a href="login.page.php">Déjà un compte ? Connectez-vous ici</a>
</body>
</html>