<?php

/*
Dans ce fichier, on définit diverses fonctions permettant de récupérer des données utiles pour notre TP d'identification. Deux parties sont à compléter, en suivant les indications données dans le support de TP
*/

// inclure ici la librairie faciliant les requêtes SQL (en veillant à interdire les inclusions multiples)
include_once("maLibSQL.pdo.php");

function sendConfirmationEmail($email, $token) {
    $subject = "Confirmation de votre email";
    $message = "Cliquez sur le lien suivant pour confirmer votre email : ";
    $message .= "http://localhost/TWE2024/projet%20WEB/index.php?view=confirm&token=" . urlencode($token);
    $headers = "From: noreply@blabla2i.com";

    mail($email, $subject, $message, $headers);
}

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

function verifCreateUser($nom,$prenom,$mail,$adress,$pass,$secondpass,$planning)
{


	$SQL = "SELECT 1 FROM users WHERE email = '$mail');";
	

	if ((strlen($nom) >= 255)||(strlen($prenom) >= 255)||(strlen($mail) >= 255)||
		(strlen($pass) >= 255)||(strlen($secondpass) >= 255)||(strlen($adress) >= 255)||(strlen($planning) >= 255)) {

		$qs = "?view=create&msg=". urlencode("Tous les champs textuels doivent contenir moins de 255 caractères.");


	} elseif (!empty(parcoursRs(SQLSelect($SQL)))) {

		$qs = "?view=create&msg=". urlencode("Adresse mail déjà utilisée");

	}

	elseif ($pass !== $secondpass) {

		$qs = "?view=create&msg=". urlencode("Les deux mots de passe sont différents.");

	}

	elseif (substr($mail, -strlen("@centrale.centralelille.fr")) !== "@centrale.centralelille.fr") { 

		$qs = "?view=create&msg=". urlencode("L'adresse mail doit être une adresse centrale (nom.prenom@centrale.centralelille.fr) ");

	} else {

	
	$hashed_password = password_hash($pass, PASSWORD_BCRYPT);
	$token = generateToken();

	
	$SQL = "INSERT INTO users (
			lastname, firstname, email, password, planninglink, adress, role, 
			confirmation_token, confirmed, confirmation_send_at, 
			reset_token, reset_send_at
		) VALUES (
			'$nom', 
			'$prenom', 
			'$mail', 
			'$hashed_password', 
			'$planning', 
			'$adress', 
			0, 
			'$token', 
			0, 
			NOW(), 
			NULL, 
			NULL
		);";


	sendConfirmationEmail($mail, $token);
	
	SQLInsert($SQL);
	$qs = "?view=login&msg=". urlencode("Utilisateur crée avec succès !");
	
	}

	return $qs;
	

}

function isAdmin($idUser)
{
	$SQL = "SELECT role from users
	WHERE id = '$idUser'";

	return SQLGetChamp($SQL);

}


/**
 * Ajoute une nouvelle voiture dans la base de données pour un utilisateur donné
 * @param string $registration La plaque d'immatriculation de la voiture
 * @param int $idUser L'identifiant de l'utilisateur
 * @return void
 */
function addCar($registration, $idUser) {

	$SQL = "SELECT 1 FROM vehicles WHERE registration = '$registration');";
	if (!empty(parcoursRs(SQLSelect($SQL)))) {

		$qs = "?view=create&msg=". urlencode("Adresse mail déjà utilisée");

	}else{

	$SQL = "INSERT INTO vehicles (registration, owner_id) VALUES ('$registration', '$idUser')";
	SQLInsert($SQL);
	$qs = "?view=login&msg=". urlencode("Utilisateur crée avec succès !");
	}

	return $qs;
}


/**
 * Récupère les voitures d'un utilisateur
 * @param int $idUser L'identifiant de l'utilisateur
 * @return array La liste des voitures de l'utilisateur
 */
function getUserCar($idUser) {
	$SQL = "SELECT registration FROM vehicles WHERE owner_id = '$idUser'";
	return parcoursRs(SQLSelect($SQL));
}

/**
 * Récupère les voitures (dans les faits 1 seule) d'un trajet
 * @param int $idTrip L'identifiant du trajet
 * @return array La liste des voitures pour le trajet
 */
function getTripCar($idTrip){
	$SQL = "SELECT v.registration FROM vehicles v JOIN trips t ON v.id = t.vehicle_id WHERE t.id = '$idTrip'";
	return parcoursRs(SQLSelect($SQL));
}


?>
