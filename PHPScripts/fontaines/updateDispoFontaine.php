<?php
    session_start();
    $fontaineID = isset($_POST['fontaineID']) ? $_POST['fontaineID'] : "";

    $fontaineID = intval($fontaineID);
    
    $fontaineEstDispo = fontaineEstDispo($fontaineID);

    if ($fontaineEstDispo) {
        rendreFontaineIndispo($fontaineID);
    }
    else {
        rendreFontaineDispo($fontaineID);
    }
    exit();

    function fontaineEstDispo($fontaineID) {
        require("../connectDB.php");
        $sql = "SELECT * FROM FONTAINE WHERE ID=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
			$bool = $commande->execute();
			if ($bool) {
				$resultat = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
            }
		}

		catch (PDOException $e) {
			header("Location: ../../signup.page.php?error=erreurBD");
			exit();
		}
        return intval($resultat[0]['Disponible']);
    }

    function rendreFontaineIndispo($fontaineID) {
        require("../connectDB.php");
        $sql = "UPDATE FONTAINE SET Disponible=0 WHERE ID=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
            $commande->execute();
		}

		catch (PDOException $e) {
			header("Location: ../../home.page.php?error=erreurBD");
			exit();
		}
    }

    function rendreFontaineDispo($fontaineID) {
        require("../connectDB.php");
        $sql = "UPDATE FONTAINE SET Disponible=1 WHERE ID=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
            $commande->execute();
		}
		catch (PDOException $e) {
			header("Location: ../../home.page.php?error=erreurBD");
			exit();
		}
    }