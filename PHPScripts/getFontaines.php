<?php
    $groupeID = isset($_POST["groupe"]) ? $_POST["groupe"] : null;  
    $userID = isset($_POST["user"]) ? $_POST["user"] : null;  

    $fontaines = array();

    if (isset($_POST["groupe"]) && isset($_POST["user"])) {
       getFontainesGroupeBu($groupeID, $userID, $fontaines);
    }
    else if (isset($_POST["groupe"])) {
        getFontainesGroupePasBu($groupeID, $userID, $fontaines);
    }
    else if (isset($_POST["user"])) {
        getFontainesGeneriqueBu($userID, $fontaines);
    }
    else {
        getFontainesGeneriquesPasBu($userID, $fontaines);
    }

    header('Content-Type: application/json');
    echo json_encode($fontaines);
    exit;

function getFontainesGroupeBu($groupeID, $userID, &$fontaines = array()) {
    require "connectDB.php";
    $sql = "SELECT * FROM FONTAINE WHERE ID_Groupe=:groupeID AND ID IN (SELECT ID_Fontaine FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID)";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':groupeID', $groupeID);
    $commande->bindparam(':userID', $userID);

    try {
        $bool = $commande->execute();
        if ($bool) {
            $fontaines = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
        }
    }
    catch (PDOException $e) {
        header("Location: ../home.page.php?error=erreurBD");
        exit();
    }
}

function getFontainesGeneriquesBu($userID, &$fontaines = array()) {
    require "connectDB.php";
    $sql = "SELECT * FROM FONTAINE WHERE ISNULL(ID_Groupe)=1 AND ID IN (SELECT ID_Fontaine FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID)";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':user', $userID);

    try {
        $bool = $commande->execute();
        if ($bool) {
            $fontaines = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
        }
    }
    catch (PDOException $e) {
        header("Location: ../home.page.php?error=erreurBD");
        exit();
    }
}

function getFontainesGeneriquesPasBu($userID, &$fontaines = array()) {
    require "connectDB.php";
    $sql = "SELECT * FROM FONTAINE WHERE ISNULL(ID_Groupe)=1 AND ID NOT IN (SELECT ID_Fontaine FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID)";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':user', $userID);

    try {
        $bool = $commande->execute();
        if ($bool) {
            $fontaines = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
        }
    }
    catch (PDOException $e) {
        header("Location: ../home.page.php?error=erreurBD");
        exit();
    }
}

function getFontainesGroupePasBu($groupeID, $userID, &$fontaines = array()) {
    require "connectDB.php";
    $sql = "SELECT * FROM FONTAINE WHERE ID_Groupe=:groupe AND ID NOT IN (SELECT ID_Fontaine FROM FONTAINES_BUES WHERE ID_Utilisateur=:userID)";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':groupeID', $groupeID);
    $commande->bindparam(':userID', $userID);
    try {
        $bool = $commande->execute();
        if ($bool) {
            $fontaines = $commande->fetchAll(PDO::FETCH_ASSOC); // Tableau de la BD
        }
    }
    catch (PDOException $e) {
        header("Location: ../home.page.php?error=erreurBD");
        exit();
    }
}