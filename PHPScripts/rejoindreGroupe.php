<?php
    session_start();
    $userID = $_SESSION['profil']['ID'];
    $codeGroupe = isset($_POST['codeGroupe']) ? $_POST['codeGroupe'] : '';
    $codeGroupe = strtoupper($codeGroupe);


	if (verifChampVide($codeGroupe)) {
		header("Location: ../params.page.php?error=champVide");
		exit();
	}
	if (verifCodePas5Chars($codeGroupe)) {
		header("Location: ../params.page.php?error=code5PasChars");
		exit();
	}
	if (verifCodeInexistant($codeGroupe)) {
		header("Location: ../params.page.php?error=codeInexistant");
		exit();
	}
	
	updateUtilisateur($userID, $codeGroupe);

	function verifChampVide($codeGroupe) {
		return $codeGroupe === '';
	}

	function verifCodePas5Chars($codeGroupe) {
		return strlen($codeGroupe) != 5;
	}


	function verifCodeInexistant($codeGroupe) {
		// Connection a la BD ci-dessous
		require("connectDB.php");
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
			header("Location: ../signup.page.php?error=erreurBD");
			exit();
		}
		if (count($resultat) > 0) {
			return false;
		}
	    return true;
	}

    function updateUtilisateur($userID, $codeGroupe) {
        $_SESSION['data'] = array(
            "userID" => (int) $userID,
            "codeGroupe" => $codeGroupe);
        require("./updateGroupeUtilisateur.php");
    }