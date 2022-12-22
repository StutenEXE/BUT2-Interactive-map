<?php
    session_start();
    $pseudo = $_SESSION['profil']['Pseudo'];
    $id = $_SESSION['profil']['ID'];
    $ID_Groupe = $_SESSION['profil']['ID_Groupe'];
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Fontaines à Paris</title>

        <!-- Style leaflet -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
            integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin="" />
        <!-- Style Esri Leaflet Geocoder from CDN -->
        <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@3.1.3/dist/esri-leaflet-geocoder.css"
            integrity="sha512-IM3Hs+feyi40yZhDH6kV8vQMg4Fh20s9OzInIIAc4nx7aMYMfo+IenRUekoYsHZqGkREUgx0VvlEsgm7nCDW9g==" crossorigin="">
        
        <link rel="stylesheet" href="./CSS/style.css"/>
        <link rel="stylesheet" href="./CSS/style.home.css"/>
        <link rel="stylesheet" href="./CSS/style.popup.css">
    </head>
    <body>
        <header>
            <h1>Bienvenue sur notre carte interactive - <?php echo $pseudo ?></h1>
            <a href="./params.page.php">Parametres</a>
            <a href="./login.page.php">Deconnexion</a>
        </header>

        <div id="MapContainer">  
            <div id="Map"></div>
                    
            <img id="QuestionMark" src="images/questionmark.png" onclick="handleShowInformation()" alt="information">

            <div id="InformationText">
                <img id="CloseInfoText" src="images/cross.png" onclick="handleCloseInformation()" alt="close">
                <p>Ce site recense toutes les fontaines dans Paris et vous permet d'ajouter 
                des fontaines pour vos amis groupes.</p>
                <p>Pour ajouter une fontaine, double-cliquez sur la carte. Cette fontaine 
                ne sera visible que par vous et votre groupe.</p>
                <p>Pour changer le statut de disponibilité d'une fontaine, cliquez dessus puis
                cliquez sur le bouton.</p>
                <p>Pour marquer une fontaine comme une à laquelle vous avez bu, cliquez dessus 
                puis cliquez sur le bouton. Seul votre groupe et vous-même pourrez voir que vous
                avez bu à cette fontaine.</p>
            </div>

            <img id="MyPosition" src="images/bullseye.svg" onclick="setupUserPosition()" alt="bullseye" title="My Position">

            <div id="Toolbar">
                <button id="ButtonRouting" onclick="handleClickRouting()">
                    Route vers la fontaine la plus proche
                </button>
                <button id="ButtonToggleMarkersDispo" onclick="handleClickToggleMarkersDispo()">
                    <span class="togglableText">Montrer</span> les fontaines indisponibles
                </button>
                <button id="ButtonShowOnlyDrank" onclick="handleClickToggleNotDrank()">
                    <span class="togglableText">Cacher</span> les fontaines auxquelles je n'ai pas bu
                </button>
                <button id="ButtonShowAll" onclick="handleClickShowAll()">
                    Montrer tout
                </button>
            </div>

        </div>
        <footer>
            <h5>Site réalisé par Alexandre Bidaux, Alexis Montculier et Axel Brun</h5>
        </footer>

        <div id="session-data" style="display:none;">
            <span id="ID_User"><?php echo $id ?></span>
            <span id="ID_Groupe"><?php echo $ID_Groupe ?></span>
        </div>

        <!-- Script Leaflet -->
        <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"
            integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
        
        <!-- Load Esri Leaflet from CDN -->
        <script src="https://unpkg.com/esri-leaflet@3.0.8/dist/esri-leaflet.js"
            integrity="sha512-E0DKVahIg0p1UHR2Kf9NX7x7TUewJb30mxkxEm2qOYTVJObgsAGpEol9F6iK6oefCbkJiA4/i6fnTHzM6H1kEA==" crossorigin=""></script>

        <!-- Load Esri Leaflet Geocoder from CDN, Coord to adresse -->
        <script src="https://unpkg.com/esri-leaflet-geocoder@3.1.3/dist/esri-leaflet-geocoder.js"
            integrity="sha512-mwRt9Y/qhSlNH3VWCNNHrCwquLLU+dTbmMxVud/GcnbXfOKJ35sznUmt3yM39cMlHR2sHbV9ymIpIMDpKg4kKw==" crossorigin=""></script>
        
        <!-- Script JQuery  -->
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>

        <!-- Script MapsService  for routing -->
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>

        <!-- Scripts Equipe -->
        <script src="JS/map.js"></script>
    </body>
</html>
