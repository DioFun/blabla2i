<?php

/*
Dans ce fichier, on définit diverses fonctions permettant de récupérer des données utiles pour notre TP d'identification. Deux parties sont à compléter, en suivant les indications données dans le support de TP
*/

// inclure ici la librairie faciliant les requêtes SQL (en veillant à interdire les inclusions multiples)
include_once("maLibSQL.pdo.php");

// TODO : Changer les url pour avoir un base url dans la config !
function sendConfirmationEmail($email, $token, $id) {
    $subject = "Confirmation de votre email";
    $message = "Cliquez sur le lien suivant pour confirmer votre email : ";
    $message .= "http://localhost/TWE2024/projetWEB/controleur.php?action=Verify&token=" . urlencode($token)."&id=".urldecode($id);
    $headers = "From: noreply@blabla2i.com";

    mail($email, $subject, $message, $headers);
}

function sendResetEmail($email, $token, $id) {
    $subject = "Confirmation de votre email";
    $message = "Cliquez sur le lien suivant pour confirmer votre email : ";
    $message .= "http://localhost/TWE2024/projetWEB/index.php?view=repassword2&token=" . urlencode($token)."&id=".urldecode($id);
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

	$SQL = "SELECT password,id,confirmed FROM users
	WHERE email = '$login'";

	$requete = ParcoursRs(SQLSelect($SQL))[0];

	if (!$requete["confirmed"]) {
		createFlash("error", "L'adresse mail n'est pas validée");
		return false;
	}

	$hash = $requete["password"];

	if (password_verify($passe, $hash)){
		return $requete["id"];
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

function updatePassword($id,$newpass){

	$SQL = "UPDATE users SET password = '$newpass' WHERE id = '$id';";
	SQLUpdate($SQL);
}

function updateLastname($id,$newLastname){

	$SQL = "UPDATE users SET lastname = '$newLastname' WHERE id = '$id';";
	SQLUpdate($SQL);
}

function updateFirstname($id,$newFirstname){

	$SQL = "UPDATE users SET firstname = '$newFirstname' WHERE id = '$id';";
	SQLUpdate($SQL);
}

function putResetToken($id,$resetToken){

	$SQL = "UPDATE users SET reset_token = '$resetToken' WHERE id = '$id';";
	SQLUpdate($SQL);
}


function recupResetToken($id){

	$SQL = "SELECT reset_token, reset_send_at FROM users WHERE id = '$id';";
	return parcoursRs(SQLSelect($SQL));
}



function updateConfirmedMail($id){


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

	elseif (substr($mail, -strlen("centralelille.fr")) !== "centralelille.fr") {


		createFlash("error", "L'adresse mail doit être une adresse centrale (en centralelille.fr) ");
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

function getUserName($idUser){
	$SQL = "SELECT firstname, lastname
	FROM users 
	WHERE id = '$idUser'";

	return parcoursRs(SQLSelect($SQL))[0];
}

function getTripInfos($tripId){
	$SQL = "SELECT date, heure, departure
	FROM trips
	WHERE id = '$tripId'";

	return parcoursRs(SQLSelect($SQL))[0];
}

function sendUserMessage($senderId, $receiverId, $content){
	$SQL = "INSERT INTO chat_users (sender_id, receiver_id, content)
	VALUES ('$senderId', '$receiverId', '$content')";

	SQLInsert($SQL);
}

function sendTripMessage($senderId, $tripId, $content){
	$SQL = "INSERT INTO chat_users (sender_id, trip_id, content)
	VALUES ('$senderId', '$tripId', '$content')";
	
	SQLInsert($SQL);
}

function sendGeneralMessage($senderId, $content){
	$SQL = "INSERT INTO chat_users (sender_id, content)
	VALUES ('$senderId', '$content')";
	
	SQLInsert($SQL);
}

function suggestUser($debut){
	$SQL = "SELECT id, fistname, lastname
	FROM users 
	WHERE firstname LIKE '$debut' OR lastname LIKE '$debut'";

	return parcoursRS(SQLSelect($SQL));
}

function recupToken($idUser)
{
	$SQL = "SELECT confirmation_token from users
	WHERE id = '$idUser'";

	return SQLGetChamp($SQL);

}

function recupConfirmationToken($idUser)
{
	$SQL = "SELECT confirmation_token, confirmation_send_at from users
	WHERE id = '$idUser'";

	return parcoursRs(SQLSelect($SQL));

}

function putConnectionToken($idUser, $connectionToken)
{

	$SQL = "UPDATE connection_tokens SET connection_token = '$resetToken' WHERE user_id = '$id';";
	SQLUpdate($SQL);

}



/**
 * Ajoute l'URL du calendrier de l'utilisateur dans la base de données
 * @param string $calURL L'URL du calendrier
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function addCal($calURL, $idUser) {
	$SQL = "UPDATE users SET planninglink = '$calURL' WHERE id = '$idUser'";
	SQLUpdate($SQL);
	return "?view=profile&msg=". urlencode("Calendrier ajouté avec succès !");
}

/**
 * Ajoute un numéro de téléphone à l'utilisateur
 * @param string $num Le numéro de téléphone
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur

 */
function getUserInfos($idUser){
	$SQL = "SELECT lastname, firstname, email, adress FROM users WHERE id = '$idUser'";
	return parcoursRs(SQLSelect($SQL))[0];
}

/**
 * Modifie les informations d'un utilisateur -> présent dans la page profile.php
 * @param string $nom Le nom de l'utilisateur
 * @param string $prenom Le prénom de l'utilisateur
 * @param string $mail L'adresse mail de l'utilisateur
 * @param string $adress L'adresse de l'utilisateur
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function modifyInfos($nom, $prenom, $mail, $adress, $idUser) {
	$SQL = "UPDATE users SET lastname = '$nom', firstname = '$prenom', email = '$mail', adress = '$adress' WHERE id = '$idUser'";
	$modif = SQLUpdate($SQL);
	log($modif === 0);
	if ($modif === 0) {
		return "?view=profile&msg=". urlencode("Informations modifiées avec succès !");
	}else{
		return "?view=profile&msg=". urlencode("Erreur lors de la modification des informations.");
	}
}

/**
 * Ajoute une nouvelle voiture dans la base de données pour un utilisateur donné
 * @param string $registration La plaque d'immatriculation de la voiture
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function addCar($registration, $idUser) {

	$SQL = "SELECT 1 FROM vehicles WHERE registration = '$registration';";
	if (!empty(parcoursRs(SQLSelect($SQL)))) {

		$qs = "?view=create&msg=". urlencode("Voiture déjà existante !");

	}else{

	$SQL = "INSERT INTO vehicles (registration, owner_id) VALUES ('$registration', '$idUser');";
	SQLInsert($SQL);
	$qs = "?view=login&msg=". urlencode("Utilisateur crée avec succès !");
	}

	return $qs;
}

/**
 * Supprime une voiture de la base de données
 * @param int $idCar L'identifiant de la voiture
 * @param int $userId L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function deleteCar($idCar, $userId) {
	$SQL = "DELETE FROM vehicles WHERE id = '$idCar' AND owner_id = '$userId'";
	$res = SQLDelete($SQL);
	return $res;
}

/**
 * Récupère les voitures d'un utilisateur
 * @param int $idUser L'identifiant de l'utilisateur
 * @return array La liste des voitures de l'utilisateur
 */
function getUserCar($idUser) {
	$SQL = "SELECT id, registration FROM vehicles WHERE owner_id = '$idUser'";
	return parcoursRs(SQLSelect($SQL));
}

/**
 * Récupère les voitures (dans les faits 1 seule) d'un trajet
 * @param int $idTrip L'identifiant du trajet
 * @return array La liste des voitures pour le trajet
 */
function getTripCar($idTrip){
	$SQL = "SELECT v.registration FROM vehicles v JOIN trips t ON v.id = t.vehicle_id WHERE t.id = '$idTrip'";
	return parcoursRs(SQLSelect($SQL))[0];
}

/**
 * Fonction popur créer une notif en y rentrant l'id de l'utilisateur (vu qu'on met pas le message dans la bdd)
 * @param int $idUser L'identifiant de l'utilisateur
 * @return int L'identifiant de la notification créée
 */
function createNotif($idUser){
	$SQL = "INSERT INTO notifications (user_id) VALUES ('$idUser');";
	$lastId = SQLInsert($SQL);

	return $lastId;
}

/**
 * Fonction pour récupérer les notifications d'un utilisateur
 * @param int $idUser L'identifiant de l'utilisateur
 * @return array La liste des notifications de l'utilisateur
 */
function getNotif($idUser){
	$SQL = "SELECT * FROM notifications WHERE user_id = '$idUser'";
	return parcoursRs(SQLSelect($SQL));
}

/**
 * Fonction pour supprimer une notification
 * @param int $idNotif L'identifiant de la notification à supprimer
 * @return string Le message à afficher à l'utilisateur
 */
function deleteNotif($idNotif){
	$SQL = "DELETE FROM notifications WHERE id = '$idNotif'";
	$res = SQLDelete($SQL);
	return !$res;
}

/**
 * Fonction pour récupérer tous les utilisateurs
 * @return array La liste des utilisateurs
 */
function getAllUsers(){
	$SQL = "SELECT id, lastname, firstname, email, adress FROM users WHERE role = 1";
	return parcoursRs(SQLSelect($SQL));
}

/**
 * Fonction pour récupérer tous les utilisateurs bannis
 * @return array La liste des utilisateurs bannis
 */
function getAllBannedUsers(){
	$SQL = "SELECT id, lastname, firstname, email, adress FROM users WHERE role = 2";
	return parcoursRs(SQLSelect($SQL));
}

/**
 * Fonction pour bannir un utilisateur
 * @param int $idUser L'identifiant de l'utilisateur à bannir
 * @return bool | int Le résultat de la requête
 */
function banUser($idUser){
	$SQL = "UPDATE users SET role = 2 WHERE id = '$idUser'";
	$res = SQLUpdate($SQL);
	return $res;
}

/**
 * Fonction pour débannir un utilisateur
 * @param int $idUser L'identifiant de l'utilisateur à débannir
 * @return bool | int Le résultat de la requête
 */
function unBanUser($idUser){
	$SQL = "UPDATE users SET role = 1 WHERE id = '$idUser'";
	$res = SQLUpdate($SQL);
	return $res;
}
?>
