<?php

    include_once('geoPHP/geoPHP.inc');
    function wkb_to_json($wkb) {
        $geom = geoPHP::load($wkb,'wkb');
        return $geom->out('json');
    }

    $groupeID = isset($_GET["groupeID"]) ? $_GET["groupeID"] : null;  
    $userID = isset($_GET["userID"]) ? $_GET["userID"] : null;  

    $groupeID = intval($groupeID);
    $userID = intval($userID);

    $fontaines = array();

    getFontaines($groupeID, $userID, $fontaines);

    header('Content-Type: application/json');
    echo json_encode($fontaines);
    exit;


function getFontaines($groupeID, $userID, &$fontaines = array()) {
    require "connectDB.php";
    $sql = "SELECT F.ID, F.Disponible, F.Rue, AsWKB(F.Coords) AS Coords, F.ID_Groupe, FB.ID_Utilisateur 
            FROM FONTAINE F LEFT JOIN FONTAINES_BUES FB ON FB.ID_Fontaine=F.ID 
            WHERE (ISNULL(F.ID_Groupe)=1 OR F.ID_Groupe=:groupeID)";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':groupeID', $groupeID);

    try {
        $bool = $commande->execute();
        if ($bool) {
            while ($row = $commande->fetch(PDO::FETCH_ASSOC)) {
                $fontaine = $row;
                unset($fontaine['Coords']);
                unset($fontaine['Disponible']);

                $fontaine["Coords"] = json_decode(wkb_to_json($row['Coords']));
                $fontaine["Disponible"] = $row["Disponible"] == "1" ? true : false;
                array_push($fontaines, $fontaine);
            }
        }
    }
    catch (PDOException $e) {
        header("Location: ../home.page.php?error=erreurBD");
        exit();
    }
}
