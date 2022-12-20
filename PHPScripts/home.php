<?php
    session_start();
    $nom = $_SESSION['profil']['pseudo'];
    header("Location: ../home.html");
?>