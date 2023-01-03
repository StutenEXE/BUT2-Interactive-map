<?php
	// SCRIPT DE CONNEXION A LA BD		

	$hostname = "localhost";
	$base= "lex-a";
	$loginBD= "root";
	$passBD="root";

    try {
	    $pdo = new PDO ("mysql:server=$hostname; dbname=$base", "$loginBD", "$passBD");
    }

    catch (PDOException $e) {
        die  ("Echec de connexion : " . $e->getMessage() . "\n");
    }
?>