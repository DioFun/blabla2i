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

function putResetToken($resetMail,$resetToken){

	$SQL = "UPDATE users SET reset_token = '$resetToken', reset_send_at = NOW() WHERE email = '$resetMail';";
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

	$SQL = "UPDATE connection_tokens SET connection_token = '$connectionToken' WHERE user_id = '$idUser';";
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
function createTrip($isDriving, $departure, $arrival, $date, $hour, $passengers, $vehicle)
{

	$id = valider("idUser", "SESSION");
	$driving = $isDriving ? $id : "NULL";
	$car = $vehicle == "Aucune" ? "NULL" : "$vehicle";
	$bddCar = $car == "NULL" ? NULL : getCarOwner($car);
	if ($car != "NULL" && $bddCar != $id && $bddCar != 1) return false;
	if ($car != "NULL" && !isCarAvailable($car, $date)) return false;
	$SQL = "INSERT INTO trips (vehicle_id, driver_id, creator_id, departure, arrival, date, hour, passenger, status) VALUES ($car, $driving, $id, '$departure', '$arrival', '$date', '$hour', '$passengers', 0)";
	$trip_id = SQLInsert($SQL);
	if (!$trip_id) return false;
	$SQL = "INSERT INTO passengers (user_id, trip_id) VALUES ('$id', '$trip_id') ";
	SQLInsert($SQL);
	return true;
}

function getTrip($id)
{
	$SQL = "SELECT * FROM trips WHERE id = '$id'";
	return parcoursRs(SQLSelect($SQL))[0];
}

function getUserTrips($id)
{
	$SQL = "SELECT departure, arrival, email, trips.id FROM trips JOIN passengers ON trips.id = passengers.trip_id JOIN users ON users.id = trips.creator_id WHERE passengers.user_id = '$id'";
	return parcoursRs(SQLSelect($SQL));
}

function getAvailableTrips($id)
{
	$SQL = "SELECT trips.id FROM trips JOIN passengers ON trips.id = passengers.trip_id WHERE passengers.user_id = '$id'";
	$SQL = "SELECT departure, arrival, email, trips.id FROM trips JOIN passengers ON trips.id = passengers.trip_id JOIN users ON users.id = trips.creator_id WHERE trips.id NOT IN ({$SQL}) ";
	return parcoursRs(SQLSelect($SQL));

}

function getPassengers($id)
{
	$SQL = "SELECT users.id, firstname, lastname FROM passengers JOIN users on user_id = users.id WHERE trip_id = '$id'";
	return parcoursRs(SQLSelect($SQL));
}

function removeTrip($id)
{
	$SQL = "DELETE FROM trips WHERE id = '$id'";
	return SQLDelete($SQL);
}

function editTrip($id, $isDriving, $departure, $destination, $date, $time, $passengers, $vehicle)
{
	$userId = valider("idUser", "SESSION");
	$driving = $isDriving ? $userId : "NULL";
	$car = $vehicle == "Aucune" ? "NULL" : "$vehicle";
	$bddCar = $car == "NULL" ? NULL : getCarOwner($car);
	if ($car != "NULL" && $bddCar != $id && $bddCar != 1) return false;
	if ($car != "NULL" && !isCarAvailableTripBypass($car, $date, $id)) return false;
	$SQL = "UPDATE trips SET driver_id = $driving, departure = '$departure', arrival='$destination', date='$date', hour='$time', passenger='$passengers', vehicle_id = $car WHERE id = '$id'";
	return SQLUpdate($SQL);
}

function getAvailableCars($date)
{
	$id = valider("idUser", "SESSION");
	$SQL = "SELECT DISTINCT vehicles.id, registration FROM vehicles LEFT JOIN trips ON vehicle_id = vehicles.id WHERE (trips.id IS NULL OR (trips.id IS NOT NULL AND date<>'$date')) AND (owner_id = '$id' OR owner_id = 1);";
	return parcoursRs(SQLSelect($SQL));
}

function getAvailableCarsTripBypass($date, $tripId)
{
	$id = valider("idUser", "SESSION");
	$SQL = "SELECT DISTINCT vehicles.id, registration FROM vehicles LEFT JOIN trips ON vehicle_id = vehicles.id WHERE (trips.id IS NULL OR ((trips.id IS NOT NULL AND date<>'$date') OR trips.id = '$tripId')) AND (owner_id = '$id' OR owner_id = 1) ;";
	return parcoursRs(SQLSelect($SQL));
}

function getCarOwner($id)
{
	$SQL = "SELECT owner_id FROM vehicles WHERE id = '$id'";
	return SQLGetChamp($SQL);
}

function getCar($id)
{
	$SQL = "SELECT * FROM vehicles WHERE id = '$id'";
	return parcoursRs(SQLSelect($SQL))[0];
}

function isCarAvailable($id, $date)
{
	$userId = valider("idUser", "SESSION");
	$SQL = "SELECT vehicles.id FROM vehicles LEFT JOIN trips ON vehicle_id = vehicles.id WHERE (trips.id IS NULL OR (trips.id IS NOT NULL AND date<>'$date')) AND (owner_id = '$userId' OR owner_id = 1) AND vehicles.id = '$id';";
	return SQLGetChamp($SQL);
}

function isCarAvailableTripBypass($id, $date, $tripId)
{
	$userId = valider("idUser", "SESSION");
	$SQL = "SELECT vehicles.id FROM vehicles LEFT JOIN trips ON vehicle_id = vehicles.id WHERE (trips.id IS NULL OR ((trips.id IS NOT NULL AND date<>'$date') OR trips.id = '$tripId')) AND (owner_id = '$userId' OR owner_id = 1) AND vehicles.id = '$id' ";

	return SQLGetChamp($SQL);
}

function joinTrip($id)
{
	$userId = valider("idUser", "SESSION");
	if (isPassenger($id) || count(getPassengers($id)) >= getTrip($id)['passenger']) return false;
	$SQL = "INSERT INTO passengers (user_id, trip_id) VALUES ('$userId', '$id')";
	return SQLInsert($SQL);
}

function isPassenger($id)
{
	$userId = valider("idUser", "SESSION");
	$SQL = "SELECT id FROM passengers WHERE user_id = '$userId' AND trip_id = '$id'";
	if (SQLGetChamp($SQL)) return true;
	return false;
}

function removePassenger($userId, $tripId)
{
	$id = valider("idUser", "SESSION");
	$trip = getTrip($tripId);
	if ($trip['creator_id'] == $userId) return false;
	if ($id != $userId && $id != $trip['creator_id']) return false;
	$SQL = "DELETE FROM passengers WHERE user_id = '$userId' AND trip_id = '$tripId'";
	return SQLDelete($SQL);
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

