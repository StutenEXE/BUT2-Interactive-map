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

    <p></p>

    <a href="login.page.php">Déjà un compte ? Connectez-vous ici</a>
</body>
</html>