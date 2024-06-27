<?php

include_once "maLibUtils.php";	// Car on utilise la fonction valider()
include_once "modele.php";	// Car on utilise la fonction connecterUtilisateur()

/**
 * @file login.php
 * Fichier contenant des fonctions de vérification de logins
 */

/**
 * Cette fonction vérifie si le login/passe passés en paramètre sont légaux
 * Elle stocke les informations sur la personne dans des variables de session : session_start doit avoir été appelé...
 * Infos à enregistrer : pseudo, idUser, heureConnexion, isAdmin
 * Elle enregistre l'état de la connexion dans une variable de session "connecte" = true
 * L'heure de connexion doit être stockée au format date("H:i:s") 
 * @pre login et passe ne doivent pas être vides
 * @param string $login
 * @param string $password
 * @return false ou true ; un effet de bord est la création de variables de session
 */
function verifUser($login,$password)
{



	$id = verifUserBdd($login,$password);

	

	if ($id) {
		$connectionToken = generateToken();
		putConnectionToken($id, $connectionToken);
		$_SESSION["connectionToken"] = $connectionToken;
		$_SESSION["idUser"] = $id;
		$_SESSION["pseudo"] = $login;
		$_SESSION["heureConnexion"] = date("H:i:s");
		$_SESSION["isAdmin"] = isAdmin($id);
		$_SESSION["connecte"] = true;
		return true;
	} 
	else {return false;}



}
// Renvoie true si les tokens correspondent et false 
function verifConnectionToken(){

	$id = valider("idUser", "SESSION");

	$SQL = "SELECT connexion_token,send_at FROM connection_tokens WHERE user_id = '$id';";
	$tokenConnectionServer = parcoursRs(SQLSelect($SQL));
	
	$currentTimestamp = time();


	foreach ($tokenConnectionServer as $tokenEntry) {
        

		// Convertir send_at en timestamp.
		$sendAtTimestamp = strtotime($tokenEntry['send_at']);

		// Vérifier si le token correspond et si la date d'envoi est inférieure à 1 jour avant la date actuelle.
		if (valider("connectionToken", "SESSION") === $tokenEntry['connexion_token'] && ($currentTimestamp - $sendAtTimestamp) < 86400) {
			// Si un token correspondant est trouvé et la date d'envoi est valide
			$_SESSION["isAdmin"] = isAdmin($id);
			return true;
		}
        
    }

	return false;
}


/**
 * Fonction à placer au début de chaque page privée
 * Cette fonction redirige vers la page $urlBad en envoyant un message d'erreur 
	et arrête l'interprétation si l'utilisateur n'est pas connecté
 * Elle ne fait rien si l'utilisateur est connecté, et si $urlGood est faux
 * Elle redirige vers urlGood sinon
 */
function securiser($urlBad,$urlGood=false)
{

}

?>
