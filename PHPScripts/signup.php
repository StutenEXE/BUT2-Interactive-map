<?php
	session_start();

	$pseudo = isset($_POST['pseudo'])?($_POST['pseudo']):'';
	$mdp = isset($_POST['mdp'])?($_POST['mdp']):'';
	$msg = '';

	if  (count($_POST)==0)
		require ("../signup.html") ;
    else {
	    if  (! verif_pseudo_ok($pseudo)) {
	        $msg = "Pseudo déjà pris";
			// $_SESSION EST UNE NORME/CONVENTION, COMME $_POST, C'EST COMME CA
			$_SESSION['profil'] = array();
	        header("Location: ../signup.html"); 
		}
	    else { 
            require("connect.php");
            $sql = "INSERT INTO UTILISATEUR(ID,Pseudo,MDP,ID_Groupe) VALUES(NULL,:pseudo,:mdp,NULL)";
			$commande = $pdo->prepare($sql);
			$commande->bindparam(':pseudo', $pseudo);
            $commande->bindparam(':mdp', $mdp);
            $commande->execute();

			$_SESSION['profil'] = array("pseudo" => $pseudo);
			echo("bienvenue");
			header("Location:home.php");
		}
    }	
	
	function verif_pseudo_ok($pseudo) {
		// Connextion a la BD ci-dessous
		require("connect.php");
		$sql = "SELECT * FROM `utilisateur` where Pseudo=:pseudo";
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
			echo utf8_encode("Echec de select : " . $e->getMessage() . "\n");
			die();
		}

		if (count($resultat) == 0) {
			return true;
		}
	    return false;
	}
?>