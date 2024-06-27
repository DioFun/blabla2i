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

function getGeneralMessages(){

	/*
	$SQL = "SELECT u.firstname, u.lastname, u.id, cg.idsender, cg.content, cg.created_at FROM chat_global AS cg 
	INNER JOIN users AS u 
	ON cg.sender_id = u.id 
	WHERE cg.deleted_at IS NULL
	ORDER BY created_at DESC";
	*/
	$SQL = "SELECT sender_id, content, created_at
	FROM chat_global
	WHERE deleted_at IS NULL
	ORDER BY created_date DESC";

	return parcoursRs(SQLSelect($SQL));
}

function getUserMessages($user1Id,$user2Id){
	$SQL = "SELECT sender_id, content, created_at
	FROM chat_users
	WHERE (receiver_id = '$user1Id' OR sender_id = '$user1Id') AND (receiver_id = '$user2Id' OR sender_id = '$user2Id') AND deleted_at IS NULL
	ORDER BY created_at DESC";

	return parcoursRs(SQLSelect($SQL));
}

function getTripMessages($tripId){
	$SQL = "SELECT sender_id, content, created_at
	FROM chat_trips
	WHERE trip_id = '$tripId' AND deleted_at IS NULL
	ORDER BY created_at DESC";

	return parcoursRs(SQLSelect($SQL));
}

function getSenderConversations($idUser){ // A priori useless mais je laisse ça là en attendant de test
	$SQL = "SELECT u.id AS userId, firstname, lastname, content, ct.created_at AS created_at
	FROM chat_user AS cu
	INNER JOIN users AS u ON cu.receiver_id = u.id
	WHERE cu.sender_id = $idUser
	ORDER BY cu.created_at DESC";

	return parcoursRs(SQLSelect($SQL));		
}

function getReceiverConversations($idUser){ // A priori useless mais je laisse ça là en attendant de test
	$SQL = "SELECT u.id AS userId, firstname, lastname, content, cu.created_at AS created_at
	FROM chat_user AS cu
	INNER JOIN users AS u ON cu.sender_id = u.id
	WHERE cu.receiver_id = $idUser
	GROUP BY userId
	ORDER BY cu.created_at DESC";

	return parcoursRs(SQLSelect($SQL));
}

// On cherche le dernier message qui implique idUser (qu'il soit sender ou receiver)
function getUserConversations($idUser){
	$SQL = "SELECT u.id AS userId, firstname, lastname, content, cu.created_at AS created_at
	FROM (SELECT u.id, firstname, lastname, content, cu.created_at FROM chat_user AS cu INNER JOIN users AS u ON cu.sender_id = u.id WHERE cu.receiver_id = '$idUser')
	UNION (SELECT u.id AS userId, firstname, lastname, content, cu.created_at AS created_at FROM chat_user AS cu INNER JOIN users AS u ON cu.sender_id = u.id WHERE cu.receiver_id = '$idUser')
	GROUP BY userId
	ORDER BY cu.created_at DESC";

	return parcoursRs(SQLSelect($SQL));
}

function getActiveTripConversations($idUser){
	// On prend la date et l'heure pour le nom de la conversation
	$SQL = "SELECT ct.trip_id AS tripId, t.date, t.heure, t.departure, firstname, lastname, content, ct.created_at AS created_at
	FROM chat_trips AS ct
	INNER JOIN passengers AS p ON ct.trip_id = p.trip_id
	INNER JOIN trips AS t ON ct.trip_id = t.id
	INNER JOIN users AS u ON ct.sender_id = u.id
	WHERE (p.user_id = '$idUser' OR t.driver_id = '$idUser') AND ct.deleted_at IS NULL AND t.status != 2
	GROUP BY ct.trip_id
	ORDER BY ct.created_at DESC";

	return parcoursRs(SQLSelect($SQL));
}

function getGeneralConversation(){
	$SQL = "SELECT firstname, lastname, content, cg.created_at AS created_at
	FROM chat_global AS cg
	INNER JOIN users AS u ON cg.sender_id = u.id 
	WHERE cg.deleted_at IS NULL
	ORDER BY created_at DESC
	LIMIT 1";

	return parcoursRs(SQLSelect($SQL));
}

?>