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
    <button onclick="window.location.href='./home.page.php'">Retour</button>
    <hr>
    <form id="FormChangerPseudo" action="PHPScripts/?" method="post">
        
    </form>
    <hr>
    <form id="FormChangeMDP" action="PHPScripts/?" method="post">
        
    </form>
    <hr>
    <!-- Formulaire de creation de groupe -->
    <form id="FormCreerGroupe" action="./PHPScripts/groupes/creerGroupe.php" method="post">
        <input name="nomGroupe" type="text"> Nom du groupe
        <input name="codeGroupe" type="text"> Code du groupe
        <button type="submit" onclick="reload()"> Soumettre </button>
    </form>
    <hr>
    <!-- Formulaire d'intégration à un groupe -->
    <form id="FormRejoindreGroupe" action="./PHPScripts/groupes/rejoindreGroupe.php" method="post">
        <input name="codeGroupe" type="text"> Code du groupe
        <button type="submit" onclick="reload()"> Soumettre </button>
    </form>
    <hr>
    <!-- Messages d'erreur -->
    <p class="error-message" style="display:<?php echo isset($_GET["error"]) ? "block" : "none"?>;" >
    <?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "champVide") {
                echo "Veuillez renseigner tous les champs";
            }
            else if ($_GET["error"] == "codeInexistant")  {
                echo "Ce code de groupe n'existe pas";
            }
            else if ($_GET["error"] == "codePas5Chars") {
                echo "Le code du groupe doit faire 5 caractères";
            }
            else if ($_GET["error"] == "nomSup20Chars") {
                echo "Le nom du groupe doit de moins de 20 caractères";
            }
            else if ($_GET["error"] == "codeExisteDeja") {
                echo "Ce code de groupe est déjà pris";
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
    <hr>
    <!-- Formulaire de départ d'un groupe -->
    <form id="FormQuitterGroupe" action="./PHPScripts/groupes/quitterGroupe.php">
        <button type="submit">Quitter groupe</button>
    </form>
    <hr>
</body>
<script src="./JS/parametres.js"></script>
</html>