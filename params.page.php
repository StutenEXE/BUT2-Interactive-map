<?php
    if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    require("./PHPScripts/updateSessionVar.php");
    putUserInSessionVar(NULL);
    $userID = $_SESSION['profil']['ID'];
    $groupeID = isset($_SESSION['profil']['ID_Groupe']) ? $_SESSION['profil']['ID_Groupe'] : "" ;
    $groupeName = $_SESSION['profil']['NomGroupe'];
    $groupeCode = $_SESSION['profil']['CodeGroupe'];
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
    <title>Paramètres</title>

    <link rel="stylesheet" href="./CSS/style.css"/>
    <link rel="stylesheet" href="./CSS/style.params.css"/>
    <link rel="stylesheet" href="./CSS/style.copierbtn.css"/>
</head>
<body>
    <div class="container">
        <div class="thin-scrollbar">
            <div class="button-form">
                <!-- Formulaire pour quitter la page -->
                <form id="FormQuitterPage" action="./PHPScripts/quitterPageParams.php" method="post">
                    <button id="BtnRetour" type="submit" onclick="window.location.href='./home.page.php'">
                        <img id="flech-back" src="./images/fleche-back.png"></img>
                        Retour
                    </button>
                </form>
                <div class="messageGroupe">
                    <p>Groupe actuel : </p>
                    <h3 id="TitreNomGroupe"><?php echo $groupeName == null ? "Vous n'avez pas de groupe" : $groupeName ?></h3>
                </div>
                <!-- Formulaire de départ d'un groupe -->
                <div class="slot" style="width:150px;">
                <?php echo $groupeName != null ?
                "<form id='FormQuitterGroupe' action='./PHPScripts/groupes/quitterGroupe.php'>
                    <button id='BoutonQuitterGroupe' type='submit' onclick='refreshGroupe()'>Quitter groupe</button>
                </form>" : "";
                ?>
                </div>
            </div>
            <!-- Bouton de copie du code de groupe -->
            <?php echo $groupeName != null ?
            "<div id='CopierCode'>
                <p>Clique pour copier ton code de groupe</p>
                <input type='checkbox' id='copy' />
                <label id='copy-btn' onclick='copyCodeToClipboard()'>$groupeCode</label>
            </div>" : "";
            ?>
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
            <!-- Formulaire de creation de groupe -->
            <div class="text-form first-text-form">
                <h3 class="form-title">Créer un groupe</h3>
                <form class="form" id="FormCreerGroupe" action="./PHPScripts/groupes/creerGroupe.php" method="post">
                    <div class="fields">    
                        <div class="input-field">
                            Nom du groupe : <input name="nomGroupe" type="text">
                        </div>
                        <div class="input-field">
                            Code du groupe : <input name="codeGroupe" type="text"> 
                        </div>
                    </div>
                    <button class="submit" type="submit" onclick="refreshGroupe()"> Soumettre </button>
                </form>
            </div>
            <!-- Formulaire d'intégration à un groupe -->
            <div class="text-form">
                <h3 class="form-title">Rejoindre un groupe</h3>
                <form class="form" id="FormRejoindreGroupe" action="./PHPScripts/groupes/rejoindreGroupe.php" method="post">
                    <div class="fields">     
                        <div class="input-field">
                            Code du groupe : <input name="codeGroupe" type="text"> 
                        </div>
                    </div>
                    <button class="submit" type="submit" onclick="refreshGroupe()"> Soumettre </button>
                </form>
            </div>

            <div id="session-data" style="display:none;">
                <span id="ID_User"><?php echo $userID ?></span>
                <span id="ID_Groupe"><?php echo $groupeID ?></span>
            </div>
        </div>
    </div>
</body>
  <!-- Script JQuery  -->
  <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="./JS/parametres.js"></script>
</html>