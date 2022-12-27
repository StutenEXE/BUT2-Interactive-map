<?php
    require("../connectDB.php");
    $sql = "UPDATE UTILISATEUR SET ID_Groupe=(SELECT ID FROM Groupe WHERE Code=:codeGroupe) WHERE ID=:userID";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':codeGroupe', $_SESSION['data']['codeGroupe']);
    $commande->bindparam(':userID', $_SESSION['data']['userID']);
    
    try {
        $commande->execute();
        require("../updateSessionVar.php");
        putUserInSessionVar(NULL);
        header("Location: ../../params.page.php");
        exit();
    }
    catch (PDOException $e) {
        header("Location: ../../params.page.php?error=erreurBD");
        exit();
    }