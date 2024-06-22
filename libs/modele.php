<?php

/*
Dans ce fichier, on définit diverses fonctions permettant de récupérer des données utiles pour notre TP d'identification. Deux parties sont à compléter, en suivant les indications données dans le support de TP
*/

// inclure ici la librairie faciliant les requêtes SQL (en veillant à interdire les inclusions multiples)
include_once("maLibSQL.pdo.php");

function sendConfirmationEmail($email, $token, $id) {
    $subject = "Confirmation de votre email";
    $message = "Cliquez sur le lien suivant pour confirmer votre email : ";
    $message .= "http://localhost/TWE2024/projet%20WEB/index.php?view=confirm&token=" . urlencode($token)."&id=".urldecode($id);
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
		return parcoursRs(SQLSelect($SQL))[0]["id"];
	}
	
	else return false;
	
	// On utilise SQLGetCHamp
	// si on avait besoin de plus d'un champ
	// on aurait du utiliser SQLSelect
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function changePassword($mail,$pass){
	
	$hashed_password = password_hash($pass, PASSWORD_BCRYPT);
	$SQL = "UPDATE users SET password = '$hashed_password' WHERE email = '$mail';";
	SQLUpdate($SQL);
}

function confirmMail($id){
	
	
	$SQL = "UPDATE users SET confirmed = 1 WHERE id = '$id';";
	SQLUpdate($SQL);


}

function verifCreateUser($nom,$prenom,$mail,$adress,$pass,$secondpass,$planning)
{


	$SQL = "SELECT id FROM users WHERE email = '$mail';";
	

	if ((strlen($nom) >= 255)||(strlen($prenom) >= 255)||(strlen($mail) >= 255)||
		(strlen($pass) >= 255)||(strlen($secondpass) >= 255)||(strlen($adress) >= 255)||(strlen($planning) >= 255)) {

		
		createFlash("error", "Tous les champs textuels doivent contenir moins de 255 caractères.");
		$qs = "?view=create";
		


	} elseif (!empty(parcoursRs(SQLSelect($SQL)))) {

		createFlash("error", "Adresse mail déjà utilisée");
		$qs = "?view=create";

	}

	elseif ($pass !== $secondpass) {

		createFlash("error", "Les deux mots de passe sont différents.");
		$qs = "?view=create";
		

	}

	elseif (substr($mail, -strlen("@centrale.centralelille.fr")) !== "@centrale.centralelille.fr") { 


		createFlash("error", "L'adresse mail doit être une adresse centrale (nom.prenom@centrale.centralelille.fr) ");
		$qs = "?view=create";
		

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


	sendConfirmationEmail($mail, $token, $id);
	
	SQLInsert($SQL);
	createFlash("success", "Utilisateur crée avec succès !");
	$qs = "?view=confirm	";
	
	
	}

	return $qs;
	

}

function isAdmin($idUser)
{
	$SQL = "SELECT role from users
	WHERE id = '$idUser'";

	return SQLGetChamp($SQL);

}

function recupToken($idUser)
{
	$SQL = "SELECT confirmation_token from users
	WHERE id = '$idUser'";

	return SQLGetChamp($SQL);

}



?>
