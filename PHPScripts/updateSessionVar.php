<?php
	if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    function putUserInSessionVar($userID) {
        $userID = $userID==NULL ? intval($_SESSION['profil']['ID']) : $userID;
		require("connectDB.php");
		$sql = "SELECT * FROM UTILISATEUR WHERE ID=:userID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':userID', $userID);

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
		$_SESSION['profil'] = $resultat[0];
	}
