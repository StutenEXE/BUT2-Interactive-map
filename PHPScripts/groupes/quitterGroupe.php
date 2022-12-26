<?php
    session_start();
    $userID = $_SESSION['profil']['ID'];

    $groupeID = getIdGroupeUtilisateur($userID);

    updateUtilisateur($userID);
    deleteGroupeSiVide($groupeID);


    function getIdGroupeUtilisateur($userID) {
        require("../connectDB.php");
        $sql = "SELECT ID_Groupe FROM UTILISATEUR WHERE ID=:userID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':userID', $userID);
        
        try {
            $bool = $commande->execute();
            if ($bool) {
                $resultat = $commande->fetchAll(PDO::FETCH_ASSOC);
                return $resultat[0]['ID_Groupe'];
            }
            else {
                header("Location: ../../params.page.php?error=pasDeGroupe");
                exit();
            }
        }
        catch (PDOException $e) {
            header("Location: ../../params.page.php?error=erreurBD");
            exit();
        }
    }

    function updateUtilisateur($userID) {
        $_SESSION['data'] = array(
            "userID" => (int) $userID,
            "codeGroupe" => NULL);
        require("./updateGroupeUtilisateur.php");
    }

    function deleteGroupesVides($groupeID) {
        require("../connectDB.php");
        $sql = "DELETE FROM GROUPE WHERE ID=:groupeID AND ISNULL(SELECT SUM(U.ID) FROM Utilisateur U WHERE U.ID_Groupe=:groupeID)=1";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(":groupeID", $groupeID);

        try {
            $commande->execute();
            header("Location: ../../params.page.php");
            exit();
        }
        catch (PDOException $e) {
            header("Location: ../../params.page.php?error=erreurBD");
            exit();
        }
    }