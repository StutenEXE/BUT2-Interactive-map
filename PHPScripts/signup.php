<?php
	session_start();

	$pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : '';
	$mdp =isset($_POST['mdp']) ? $_POST['mdp'] : '';
	$mdpVerif = isset($_POST['mdp-validation']) ? $_POST['mdp-validation'] : '';

	$profil = array();

	if (verifChampVide($pseudo, $mdp, $mdpVerif)) {
		header("Location: ../signup.page.php?error=champVide");
		exit();
	}
	if (verifPseudoInvalide($pseudo)) {
		header("Location: ../signup.page.php?error=pseudoInvalide");
		exit();
	}
	if (verifPseudoExistant($pseudo, $profil)) {
		header("Location: ../signup.page.php?error=pseudoExistant");
		exit();
	}
	if (verifMdpInf8Chars($mdp)) {
		header("Location: ../signup.page.php?error=mdpCourt");
		exit();
	}
	if (verifMdpVerifDifferent($mdp, $mdpVerif)) {
		header("Location: ../signup.page.php?error=mdpInequivalents");
		exit();
	}
	
	insertUtilisateur($pseudo, $mdp);

	$_SESSION['profil'] = array("Pseudo" => $pseudo);
	header("Location:../home.page.php");
	

	function verifChampVide($pseudo, $mdp, $mdpVerif) {
		return $pseudo === '' || $mdp === '' || $mdpVerif === '';
	}

	function verifPseudoInvalide($pseudo) {
		return !preg_match("/^[a-zA-Z0-9]*$/", $pseudo);
	}

	function verifPseudoExistant($pseudo, &$profil = array()) {
		// Connection a la BD ci-dessous
		require("connectDB.php");
		$sql = "SELECT * FROM UTILISATEUR where Pseudo=:pseudo";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':pseudo', $pseudo);
		
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
			return true;
		}
		$profil = $resultat[0];
		return false;
	}

	function verifMdpInf8Chars($mdp) {
		return strlen($mdp) < 8;
	}

	function verifMdpVerifDifferent($mdp, $mdpVerif) {
		return $mdp != $mdpVerif;
	}

    function insertUtilisateur($pseudo, $mdp) {
        require("connectDB.php");
        $sql = "INSERT INTO UTILISATEUR(ID,Pseudo,MDP,ID_Groupe) VALUES(NULL,:pseudo,:mdp,NULL)";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':pseudo', $pseudo);
        $commande->bindparam(':mdp', $mdp);
        $commande->execute();
    }
?>