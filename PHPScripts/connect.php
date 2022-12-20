<?php
	// SCRIPT DE CONNEXION A LA BD		

	$hostname = "localhost";	//ou localhost
	$base= "lex-a";
	$loginBD= "root";	//ou "root"
	$passBD="root";
	//$pdo = null;

    try {
	    $pdo = new PDO ("mysql:server=$hostname; dbname=$base", "$loginBD", "$passBD");
    }

    catch (PDOException $e) {
        die  ("Echec de connexion : " . $e->getMessage() . "\n");
    }

    // Lignes pour tester la connection
    // $ok = 'connexion ok';
    // die ($ok); 
?>