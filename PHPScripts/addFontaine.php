<?php

    $coords = isset($_GET['coordinates']) ? $_GET['coordinates'] : "";
    $disponible = isset($_GET['disponible']) ? $_GET['disponible'] : "";
    $rue = isset($_GET['rue']) ? $_GET['rue'] : "";
    $groupeID = isset($_GET['groupeID']) ? isset($_GET['groupeID']) : "";
 

    $coords = array_map('floatval', $coords);
    $disponible = filter_var($disponible, FILTER_VALIDATE_BOOLEAN);
    $groupeID = intval($groupeID);

    require("connectDB.php");
    $sql = "INSERT INTO FONTAINE(ID,Disponible,Rue,ID_Groupe,Coords) VALUES(NULL, :disponible , :rue , :groupeID ,ST_GeomFromText('POINT(  $coords[0] $coords[1] )'))";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':disponible', $disponible);
    $commande->bindparam(':rue', $rue);
    $commande->bindparam(':groupeID', $groupeID);
    
    try {
        $bool = $commande->execute();
        if ($bool) print('yes');
        else print('no');
        exit();
    }
    catch (PDOException $e) {
        exit();
    }