<?php
    if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    $groupeID = isset($_SESSION['profil']['ID_Groupe']) ? $_SESSION['profil']['ID_Groupe'] : "" ;

    $return = array();

    if ($groupeID == "") {
        $return["exist"] = false;
        echo json_encode($return);
        exit();
    }
    else {
        $return["exist"] = true;
    }

    getGroupe($groupeID, $return);
    echo json_encode($return);

    
    function getGroupe($groupeID, &$return) {
        require "../connectDB.php";
        $sql = "SELECT * FROM GROUPE WHERE ID=:groupeID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':groupeID', $groupeID);

        try {
            $bool = $commande->execute();
            if ($bool) {
                $result = $commande->fetchAll(PDO::FETCH_ASSOC); 
                $return["groupe"] = $result[0];
            }
        }
        catch (PDOException $e) {
			header("Location: ../params.page.php?error=erreurBD");
			exit();
		}
    }