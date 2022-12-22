<?php
    session_start();
    $userID = $_SESSION['profil']['ID'];

    if ($userID == null) {
        header("Location:./login.page.php");
    }
?>

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
    <form id="FormChangerPseudo" action="PHPScripts/?" method="post">
        
    </form>

    <form id="FormChangeMDP" action="PHPScripts/?" method="post">
        
    </form>

    <form id="FormCreerGroupe" action="PHPScripts/?" method="post">
        
    </form>

    <form id="FormRejoindreGroupe" action="PHPScripts/rejoindreGroupe.php" method="post">
        <input name="codeGroupe" type="text"> Code du groupe
        <button type="submit" value="soumettre" onclick="reload()"> Soumettre </button>
    </form>

    <form id="FormQuitterGroupe" action="PHPScripts/?" method="post">
        
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
</body>
<script src="./JS/parametres.js"></script>
</html>