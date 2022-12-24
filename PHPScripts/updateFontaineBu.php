<?php
    session_start();
    $userID = $_SESSION['profil']['ID'];
    $fontaineID = isset($_GET['fontaineID']) ? $_GET['fontaineID'] : "";

    $userID = intval($userID);
    $fontaineID = intval($fontaineID);
    
    $aBuDansFontaine = utilisateurDejaBuFontaine($userID, $fontaineID);

    if ($aBuDansFontaine) {
        supprimerBoire($userID, $fontaineID);
    }
    else {
        ajouterBoire($userID, $fontaineID);
    }
    exit();

    function utilisateurDejaBuFontaine($userID, $fontaineID) {
        require("connectDB.php");
        $sql = "SELECT * FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID AND ID_Fontaine=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':userID', $userID);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
			$bool = $commande->execute();

			if ($bool) {
				$resultat = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
			}
		}

		catch (PDOException $e) {
			header("Location: ../signup.page.php?error=erreurBD");
			exit();
		}

		if (count($resultat) == 0) {
			return false;
		}
		return true;
    }

    function supprimerBoire($userID, $fontaineID) {
        require("connectDB.php");
        $sql = "DELETE FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID AND ID_Fontaine=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':userID', $userID);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
            $commande->execute();
		}

		catch (PDOException $e) {
			header("Location: ../signup.page.php?error=erreurBD");
			exit();
		}
    }

    function ajouterBoire($userID, $fontaineID) {
        require("connectDB.php");
        $sql = "INSERT INTO FONTAINES_BUES(ID_Utilisateur,ID_Fontaine) VALUES(:userID, :fontaineID)";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':userID', $userID);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
			$commande->execute(); // Tableau de la BD
		}

		catch (PDOException $e) {
			header("Location: ../signup.page.php?error=erreurBD");
			exit();
		}
    }