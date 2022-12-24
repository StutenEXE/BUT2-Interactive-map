<?php
    $fontaineID = isset($_POST['fontaineID']) ? $_POST['fontaineID'] : "";

    require("connectDB.php");
    $sql = "DELETE FROM FONTAINE WHERE ID=:fontaineID";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':fontaineID', $fontaineID);
    
    try {
        $commande->execute();
        header("Location: ../params.page.php");
        exit();
    }
    catch (PDOException $e) {
        header("Location: ../params.page.php?error=erreurBD");
        exit();
    }