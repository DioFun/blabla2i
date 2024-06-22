<?php

/*
Dans ce fichier, on définit diverses fonctions permettant de récupérer des données utiles pour notre TP d'identification. Deux parties sont à compléter, en suivant les indications données dans le support de TP
*/


/********* EXERCICE 2 : prise en main de la base de données *********/


// inclure ici la librairie faciliant les requêtes SQL (en veillant à interdire les inclusions multiples)
include_once("maLibSQL.pdo.php");




function verifUserBdd($login,$passe)
{
	// Vérifie l'identité d'un utilisateur 
	// dont les identifiants sont passes en paramètre
	// renvoie faux si user inconnu
	// renvoie l'id de l'utilisateur si succès

	//$passwordHash = password_hash($passe, PASSWORD_BCRYPT);

	$SQL = "SELECT password,id FROM users
	WHERE email = '$login'";


	$hash = ParcoursRs(SQLSelect($SQL))[0]["password"];

	if (password_verify($passe, $hash)){
		return ParcoursRs(SQLSelect($SQL))[0]["id"];
	}
	
	else return false;
	
	// On utilise SQLGetCHamp
	// si on avait besoin de plus d'un champ
	// on aurait du utiliser SQLSelect
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function verifCreateUser($nom,$prenom,$mail,$pass,$secondpass)
{


	if ((strlen($nom) >= 255)||(strlen($prenom) >= 255)||(strlen($mail) >= 255)||
		(strlen($pass) >= 255)||(strlen($secondpass) >= 255)||(strlen($adress) >= 255)) {

		$qs = "?view=create&msg=". urlencode("Tous les champs textuels doivent contenir moins de 255 caractères.");


	}

	elseif ($pass !== $secondpass) {

		$qs = "?view=create&msg=". urlencode("Les deux mots de passe sont différents.");

	}

	elseif (substr($mail, -strlen("@centrale.centralelille.fr")) === "@centrale.centralelille.fr") { 

		$qs = "?view=create&msg=". urlencode("L'adresse mail doit être une adresse centrale (nom.prenom@centrale.centralelille.fr) ");

	} else {

	
	$token = generateToken();
	$SQL = "UPDATE users SET confirmation_token = '$token', confirmation_send_at = NOW() WHERE email = '$mail'";

	}

	return $qs;
	

}

function isAdmin($idUser)
{
	$SQL = "SELECT role from users
	WHERE id = '$idUser'";

	return SQLGetChamp($SQL);

}



?>
