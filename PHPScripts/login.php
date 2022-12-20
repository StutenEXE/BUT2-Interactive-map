<?php
	session_start();

	$pseudo = isset($_POST['pseudo'])?($_POST['pseudo']):'';
	$mdp = isset($_POST['mdp'])?($_POST['mdp']):'';
	$msg = '';

	$profil = array();

	if  (count($_POST)==0)
		require ("../login.html") ;
    else {
	    if  (! verif_ident($pseudo, $mdp, $profil)) {
	        $msg = "erreur de saisie";
			// $_SESSION EST UNE NORME/CONVENTION, COMME $_POST, C'EST COMME CA
			$_SESSION['profil'] = array();
	        header("Location: ../login.html"); 
		}
	    else { 
			$_SESSION['profil'] = $profil;
			echo("bienvenue");
			header("Location: home.php");
		}
    }	
	
	function verif_ident($pseudo, $mdp, &$profil=array()) {
		// Connextion a la BD ci-dessous
		require("connect.php");
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
			echo utf8_encode("Echec de select : " . $e->getMessage() . "\n");
			die();
		}

		if (count($resultat) == 0) {
			return false;
		}
		else {
			$profil = $resultat[0];
			return true;
		}
	}
?>