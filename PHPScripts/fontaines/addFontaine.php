<?php
    include_once('geoPHP/geoPHP.inc');
    function wkb_to_json($wkb) {
        $geom = geoPHP::load($wkb,'wkb');
        return $geom->out('json');
    }


    $coords = isset($_POST['coordinates']) ? $_POST['coordinates'] : "";
    $disponible = isset($_POST['disponible']) ? $_POST['disponible'] : "";
    $rue = isset($_POST['rue']) ? $_POST['rue'] : "";
    $groupeID = isset($_POST['groupeID']) ? isset($_POST['groupeID']) : "";
 

    $coords = array_map('floatval', $coords);
    $disponible = filter_var($disponible, FILTER_VALIDATE_BOOLEAN);
    $groupeID = intval($groupeID);

    // Insertion de la fontaine
    require("../connectDB.php");
    $sql = "INSERT INTO FONTAINE(ID,Disponible,Rue,ID_Groupe,Coords) VALUES(NULL, :disponible , :rue , :groupeID ,ST_GeomFromText('POINT(  $coords[0] $coords[1] )'))";
    $commande = $pdo->prepare($sql);
    $commande->bindparam(':disponible', $disponible);
    $commande->bindparam(':rue', $rue);
    $commande->bindparam(':groupeID', $groupeID);
    
    try {
        $bool = $commande->execute();
        if ($bool) {
            $fontaineID = $pdo->lastInsertId();
            getFontaine($fontaineID);
        }
        exit();
    }
    catch (PDOException $e) {
        exit();
    }

    function getFontaine($fontaineID) {
        require "../connectDB.php";
        $sql = "SELECT F.ID, F.Disponible, F.Rue, AsWKB(F.Coords) AS Coords, F.ID_Groupe, FB.ID_Utilisateur 
                FROM FONTAINE F LEFT JOIN FONTAINES_BUES FB ON FB.ID_Fontaine=F.ID 
                WHERE F.ID=:fontaineID";
        $commande = $pdo->prepare($sql);
        $commande->bindparam(':fontaineID', $fontaineID);

        try {
            $bool = $commande->execute();
            if ($bool) {
                $row = $commande->fetch(PDO::FETCH_ASSOC);
                $fontaine = $row;
                unset($fontaine['Coords']);
                unset($fontaine['Disponible']);

                $fontaine["Coords"] = json_decode(wkb_to_json($row['Coords']));
                $fontaine["Disponible"] = $row["Disponible"] == "1" ? true : false;
                echo json_encode($fontaine);
            }
        }
        catch (PDOException $e) {
            header("Location: ../../home.page.php?error=erreurBD");
            exit();
        }
    }