<?php
	session_start();

	$pseudo = isset($_POST['pseudo'])?($_POST['pseudo']):'';
	$mdp = isset($_POST['mdp'])?($_POST['mdp']):'';
	
	$profil = array();

	if (verifChampVide($pseudo, $mdp)) {
		header("Location: ../login.page.php?error=champVide");
		exit();
	}
	if  (verifUtilisateurInexistant($pseudo, $mdp, $profil)) {
		header("Location: ../login.page.php?error=informationserronees");
		exit();
	}

	$_SESSION['profil'] = $profil;
	header("Location: ../home.page.php");

	function verifChampVide($pseudo, $mdp) {
		return $pseudo === '' || $mdp === '';
	}

	function verifUtilisateurInexistant($pseudo, $mdp, &$profil=array()) {
		// Connextion a la BD ci-dessous
		require("connectDB.php");
		$sql = "SELECT * FROM `utilisateur` where Pseudo=:pseudo and MDP=:mdp";
			$commande = $pdo->prepare($sql);
			$commande->bindparam(':pseudo', $pseudo);
			$commande->bindparam(':mdp', $mdp);
		
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

		if (count($resultat) == 0) {
			return true;
		}
		$profil = $resultat[0];
		return false;
	}
?>