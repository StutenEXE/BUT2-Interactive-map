<?php
     session_start();
     $userID = $_SESSION['profil']['ID'];
     $nomGroupe = isset($_POST['nomGroupe']) ? $_POST['nomGroupe'] : '';
     $codeGroupe = isset($_POST['codeGroupe']) ? $_POST['codeGroupe'] : '';
     $codeGroupe = strtoupper($codeGroupe);
 
    
    if (verifChampVide($codeGroupe, $nomGroupe)) {
         header("Location: ../../params.page.php?error=champVide");
         exit();
    }
    if (verifCodePas5Chars($codeGroupe)) {
        header("Location: ../../params.page.php?error=codePas5Chars");
        exit();
    }
    if (verifNomSup20Chars($nomGroupe)) {
        header("Location: ../../params.page.php?error=nomSup20Chars");
        exit();
    }
    if (verifCodeExisteDeja($codeGroupe)) {
        header("Location: ../../params.page.php?error=codeExisteDeja");
        exit();
    }
    
    insertGroupe($codeGroupe, $nomGroupe);
    updateUtilisateur($userID, $codeGroupe);

    // Fonctions de controle
    function verifChampVide($codeGroupe, $nomGroupe) {
        return $codeGroupe === '' || $nomGroupe === '';
    } 

    function verifCodePas5Chars($codeGroupe) {
		return strlen($codeGroupe) != 5;
	}

    function verifCodeExisteDeja($codeGroupe) {
        require("../connectDB.php");
		$sql = "SELECT * FROM GROUPE where Code=:code";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':code', $codeGroupe );
		
		$resultat = array();
		try {
			$bool = $commande->execute();
			if ($bool) {
				$resultat = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
			}
		}
		catch (PDOException $e) {
			header("Location: ../../params.page.php?error=erreurBD");
			exit();
		}
		if (count($resultat) > 0) {
			return true;
		}
	    return false;
    }

    function verifNomSup20Chars($nomGroupe) {
        return strlen($nomGroupe) > 20;
    }


    function insertGroupe($codeGroupe, $nomGroupe) {
        require("../connectDB.php");
        $sql = "INSERT INTO GROUPE(ID, Code, Nom) VALUES(NULL, :codeGroupe, :nomGroupe)";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':codeGroupe', $codeGroupe);
        $commande->bindparam(':nomGroupe', $nomGroupe);

        try {
            $bool = $commande->execute();
        }
        catch (PDOException $e) {
            header("Location: ../../params.page.php?error=erreurBD");
            exit();
        }
    }

    function updateUtilisateur($userID, $codeGroupe) {
        $_SESSION['data'] = array(
            "userID" => (int) $userID,
            "codeGroupe" => $codeGroupe);
        require("./updateGroupeUtilisateur.php");
    }