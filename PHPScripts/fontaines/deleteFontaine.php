<?php
    $fontaineID = isset($_POST['fontaineID']) ? $_POST['fontaineID'] : "";
    $fontaineID = intval($fontaineID);

    deleteFontaineBues($fontaineID);

    deleteFontaine($fontaineID);

    function deleteFontaineBues($fontaineID) {
        require("../connectDB.php");
        $sql = "DELETE FROM FONTAINES_BUES WHERE ID_Fontaine=:fontaineID";
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

    function deleteFontaine($fontaineID) {
        require("../connectDB.php");
        $sql = "DELETE FROM FONTAINE WHERE ID=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':fontaineID', $fontaineID);
        
        try {
            $commande->execute();
            exit();
        }
        catch (PDOException $e) {
            header("Location: ../../home.page.php?error=erreurBD");
            exit();
        }
    }

    
