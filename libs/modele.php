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

?>
