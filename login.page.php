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
    <a href="signup.page.php">Pas encore de compte ? Inscirvez-vous ici</a>
</body>
</html>